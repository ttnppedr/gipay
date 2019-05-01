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

        $text = explode(',', $request['text']);
        preg_match("/^<@(\w+)\|(\w+)>$/", $text[0], $text);
        $payee = $text[1]; // 收款人

        $users = User::whereIn('slack_id', [$payer, $payee])->get();

        if ($users[0]['blocked']) {
            return response()->json(["text" => "匯款人帳號被凍結"]);
        }

        if ($users[1]['blocked']) {
            return response()->json(["text" => "收款人帳號被凍結"]);
        }

        return $next($request);
    }
}
