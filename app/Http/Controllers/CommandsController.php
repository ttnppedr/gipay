<?php

namespace App\Http\Controllers;

use App\Trigger;
use App\User;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

const TIMEOUT_SECOND = 300;

class CommandsController extends Controller
{
    public function pay(Request $request)
    {
        Log::debug(json_encode(urldecode($request->getContent()), JSON_UNESCAPED_SLASHES));

        $text = explode(',', $request['text']);

        $trigger = Trigger::create([
            'trigger_id' => $request['trigger_id'],
            'user_id' => User::where('slack_id', substr($text[0], 2, 9))->first()->id,
            'amount' => $text[1],
        ]);

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

    public function record(Request $request)
    {
        Log::debug($request);

        $orders = User::slack($request['user_id'])->first()->orders()->orderByDesc('created_at', 'desc')->take(5)->get();

        $attachments = [];
        foreach ($orders as $order) {
            switch ($order['type']) {
                case 1:
                    $attachments[] = $this->setRecord("日期：{$order['created_at']->toDateTimeString()}",
                        "存放 {$order['amount']} 元");
                    break;
                case 2:
                    $attachments[] = $this->setRecord("日期：{$order['created_at']->toDateTimeString()}",
                        "提取 {$order['amount']} 元");
                    break;
                case 3:
                    $attachments[] = $this->setRecord("日期：{$order['created_at']->toDateTimeString()}",
                        "`<@{$order->from->slack_id}>` 轉給 `<@{$order->to->slack_id}>` {$order['amount']} 元");
                    break;
            }
        }

        return response()->json([
            "text" => "查看前 " . count($attachments) . " 筆交易記錄",
            "attachments" => $attachments,
        ]);
    }

    public function balance(Request $request)
    {
        return response()->json([
            'text' => '餘額 ' . User::slack($request['user_id'])->first()->balance . ' 元']);
    }

    private function setRecord($title, $text)
    {
        return ["title" => $title, "text" => $text];
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
                    $toUser = User::find($trigger->user_id);
                    $amount = $trigger->amount;

                    if ($user->balance - $amount < 0) {
                        return response()->json(["text" => "完成失敗，餘額不足"]);
                    }

                    try {
                        DB::transaction(function () use ($user, $toUser, $amount) {
                            Order::create([
                                'type' => 3,
                                'from_user_id' => $user->id,
                                'to_user_id' => $toUser->id,
                                'amount' => $amount,
                            ]);

                            $toUser->update(['balance' => $toUser->balance + $amount]);
                            $user->update(['balance' => $user->balance - $amount, 'password_errors' => 0]);
                        });
                    } catch (\Exception $e) {
                        return response()->json(["text" => "完成失敗，資料庫錯誤"]);
                    }
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
