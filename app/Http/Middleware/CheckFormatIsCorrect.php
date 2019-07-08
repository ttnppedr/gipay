<?php

namespace App\Http\Middleware;

use Closure;

class CheckFormatIsCorrect
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
        $text = app()->make('TextExploder')->getText();

        if (count($text) === 1) {
            return response()->json(["text" => "交易失敗，格式錯誤"]);
        }

        if (!$this->checkWhoIsCorrectFormat($text[0])) {
            return response()->json(["text" => "交易失敗，Who 格式錯誤"]);
        }

        if (!$this->checkAmountIsCorrect($text[1])) {
            return response()->json(["text" => "交易失敗，Amount 格式錯誤"]);
        }

        return $next($request);
    }

    private function checkWhoIsCorrectFormat($who): bool
    {
        return preg_match("/^<@\w+\|\w+>$/", $who) ? true : false;
    }

    private function checkAmountIsCorrect($amount): bool
    {
        return preg_match("/^[0-9]+$/", $amount) ? true : false;
    }
}
