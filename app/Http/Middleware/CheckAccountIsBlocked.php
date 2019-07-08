<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class CheckAccountIsBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $payer = $request['user_id']; // 匯款人

        $text = app()->make('TextExploder')->getText();
        preg_match("/^<@(\w+)\|(\w+)>$/", $text[0], $text);
        $payee = $text[1]; // 收款人

        if ($message = $this->checkUsers(compact('payer', 'payee'))) {
            return response()->json(["text" => $message]);
        };

        $users = User::whereIn('slack_id', [$payer, $payee])->get();

        if ($users[0]['blocked']) {
            return response()->json(["text" => "匯款人帳號被凍結"]);
        }

        if ($users[1]['blocked']) {
            return response()->json(["text" => "收款人帳號被凍結"]);
        }

        return $next($request);
    }

    private function checkUsers($users)
    {
        $message = [];

        User::where('slack_id', $users['payer'])->exists() ?: $message[] = '匯款人不存在';
        User::where('slack_id', $users['payee'])->exists() ?: $message[] = '收款人不存在';

        return implode(', ', $message);
    }
}
