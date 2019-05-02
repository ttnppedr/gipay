<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommandsController extends Controller
{
    public function pay(Request $request)
    {
        Log::debug(json_encode(urldecode($request->getContent()), JSON_UNESCAPED_SLASHES));

        $text = explode(',', $request['text']);

        return response()->json($this->getPayResponse($text));
    }

    private function getPayResponse(array $text): array
    {
        return [
            "text" => "付錢囉～",
            "attachments" => [
                [
                    "text" => "您確定要將 {$text[1]} 元給 {$text[0]} 嗎？",
                    "callback_id" => "confirm_transaction",
                    "color" => "#ff0000",
                    'actions' => [
                        ['name' => 'yes', 'text' => '確定', 'type' => 'button', 'value' => 'yes', 'style' => 'danger'],
                        ['name' => 'no', 'text' => '讓我想想', 'type' => 'button', 'value' => 'no', 'style' => 'primary'],
                    ]
                ]
            ]
        ];
    }

    public function callback(Request $request)
    {
        Log::debug(str_replace('\\', '', json_encode(urldecode($request['payload']), JSON_UNESCAPED_SLASHES)));

        $payload = json_decode($request['payload'], true);

        $user = User::where('slack_id', '=', $payload['user']['id'])->first();
        $password = $user->password;

        if ($payload['callback_id'] === 'confirm_transaction') {
            if ($payload['actions'][0]['name'] === 'no') {
                return response()->json(["text" => "OK，讓你想想～"]);
            }

            if ($payload['actions'][0]['name'] === 'yes') {
                return response()->json($this->getPasswordResponse(1, $password));
            }
        }

        if ($payload['callback_id'] === 'confirm_password') {
            if ($payload['actions'][0]['name'] === '4') {
                if ($payload['actions'][0]['value'] == $password) {
                    return response()->json(["text" => "完成交易"]);
                } else {
                    $user->increment('password_errors');

                    if (($errors = $user->password_errors % 3) === 0) {
                        $user->update(['blocked' => true]);
                        return response()->json(["text" => "完成失敗，密碼錯誤，帳號被鎖住了"]);
                    }
                    $errors = 3 - $errors;
                    return response()->json(["text" => "完成失敗，密碼錯誤，可以嘗試次數剩下{$errors}次"]);
                }
            }
            return response()->json($this->getPasswordResponse($payload['actions'][0]['name'] + 1, $password, $payload['actions'][0]['value']));
        }

        return response()->json(["text" => "某些地方似乎出錯了"]);
    }

    private function getPasswordResponse($times, $password, $value = null): array
    {
        $pwd = [];
        for ($i = 0; $i < strlen($password); $i++) {
            $pwd[$i] = substr($password, $i, 1);
        }

        $number = [];
        for ($i = 0; $i < 10; $i++) {
            if ($i == $pwd[$times - 1]) {
                continue;
            }
            $number[] = $i;
        }
        shuffle($number);

        $btn = [];
        $btn[0] = $this->getResponseAction($times, $pwd[$times - 1], $value);

        for ($i = 1; $i < 5; $i++) {
            $btn[$i] = $this->getResponseAction($times, array_pop($number), $value);
        }
        shuffle($btn);

        return [
            "text" => "請輸入密碼：",
            "attachments" => [
                [
                    "text" => "第 {$times} 碼",
                    "callback_id" => "confirm_password",
                    "color" => "#ff0000",
                    'actions' => $btn
                ],
            ]
        ];
    }

    private function getResponseAction($times, $num, $value): array
    {
        return [
            "name" => $times, "text" => "$num", "type" => "button", "value" => $value === null ? "$num" : "{$value}$num"
        ];
    }
}
