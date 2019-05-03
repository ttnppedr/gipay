<?php

namespace App\Http\Controllers;

use App\Trigger;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

const TIMEOUT_SECOND = 300;

class CommandsController extends Controller
{
    public function pay(Request $request)
    {
        Log::debug(json_encode(urldecode($request->getContent()), JSON_UNESCAPED_SLASHES));

        $text = explode(',', $request['text']);

        $trigger = Trigger::create(['trigger_id' => $request['trigger_id']]);

        return response()->json($this->getPayResponse($text, $trigger->id));
    }

    private function getPayResponse(array $text, $id): array
    {
        return [
            "text" => "付錢囉～",
            "attachments" => [
                [
                    "text" => "您確定要將 {$text[1]} 元給 {$text[0]} 嗎？",
                    "callback_id" => "confirm_transaction",
                    "color" => "#ff0000",
                    "actions" => [
                        ["name" => "{$id}|yes", "text" => "確定", "type" => "button", "value" => "yes", "style" => "danger"],
                        ["name" => "{$id}|no", "text" => "讓我想想", "type" => "button", "value" => "no", "style" => "primary"],
                    ]
                ]
            ]
        ];
    }

    public function callback(Request $request)
    {
        Log::debug(str_replace('\\', '', json_encode(urldecode($request['payload']), JSON_UNESCAPED_SLASHES)));

        $payload = json_decode($request['payload'], true);

        $name = explode('|', $payload['actions'][0]['name']);;

        $trigger = Trigger::findOrFail($name[0]);
        if ($trigger->created_at->diffInSeconds() > TIMEOUT_SECOND) {
            return response()->json(["text" => "交易失敗，操作逾時"]);
        }

        $user = User::where('slack_id', '=', $payload['user']['id'])->first();

        if ($payload['callback_id'] === 'confirm_transaction') {
            if ($name[1] === 'no') {
                return response()->json(["text" => "OK，讓你想想～"]);
            }

            if ($name[1] === 'yes') {
                return response()->json($this->getPasswordResponse("{$name[0]}|1", $user->password));
            }
        }

        if ($payload['callback_id'] === 'confirm_password') {
            if ($name[1] === '4') {
                if ($payload['actions'][0]['value'] == $user->password) {
                    return response()->json(["text" => "完成交易"]);
                } else {
                    $user->increment('password_errors');

                    if ($user->password_errors >= 3) {
                        $user->update(['password_errors' => 0, 'blocked' => true]);
                        return response()->json(["text" => "完成失敗，密碼錯誤，帳號被鎖住了"]);
                    }

                    $errors = 3 - $user->password_errors;
                    return response()->json(["text" => "完成失敗，密碼錯誤，可以嘗試次數剩下{$errors}次"]);
                }
            }
            return response()->json($this->getPasswordResponse("{$name[0]}|" . ($name[1] + 1), $user->password, $payload['actions'][0]['value']));
        }

        return response()->json(["text" => "某些地方似乎出錯了"]);
    }

    private function getPasswordResponse($name, $password, $value = null): array
    {
        $name = explode('|', $name);

        $pwd = [];
        for ($i = 0; $i < strlen($password); $i++) {
            $pwd[$i] = substr($password, $i, 1);
        }

        $number = [];
        for ($i = 0; $i < 10; $i++) {
            if ($i == $pwd[$name[1] - 1]) {
                continue;
            }
            $number[] = $i;
        }
        shuffle($number);

        $btn = [];
        $btn[0] = $this->getResponseAction(implode('|', $name), $pwd[$name[1] - 1], $value);

        for ($i = 1; $i < 5; $i++) {
            $btn[$i] = $this->getResponseAction(implode('|', $name), array_pop($number), $value);
        }
        shuffle($btn);

        return [
            "text" => "請輸入密碼：",
            "attachments" => [
                [
                    "text" => "第 {$name[1]} 碼",
                    "callback_id" => "confirm_password",
                    "color" => "#ff0000",
                    'actions' => $btn
                ],
            ]
        ];
    }

    private function getResponseAction($name, $num, $value): array
    {
        return [
            "name" => $name, "text" => "$num", "type" => "button", "value" => $value === null ? "$num" : "{$value}$num"
        ];
    }
}
