<?php

namespace App\Http\Controllers;

use App\User;
use App\Token;
use App\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function login()
    {
        request()->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($user = User::where('email', request('email'))->where('admin', true)->first()) {
            if ($user->password == request('password')) {
                $token = Token::updateOrCreate(['user_id' => $user->id], ['token' => Str::random(60)]);
                return ['token' => $token->token];
            }
        }

        return ['message' => 'email or password error'];
    }

    public function deposit(User $toUser)
    {
        $token = request()->bearerToken();
        if ($token && !Token::where('token', $token)->exists()) {
            return ['message' => 'token error'];
        }

        request()->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        return DB::transaction(function () use ($toUser) {
            $order = Order::create([
                'type' => '1',
                'to_user_id' => $toUser->id,
                'amount' => request('amount'),
            ]);

            $toUser->update(['balance' => $toUser->balance + request('amount')]);

            return ['order' => $order, 'to_user' => $toUser];
        });
    }

    public function withdraw(User $toUser)
    {
        $token = request()->bearerToken();
        if ($token && !Token::where('token', $token)->exists()) {
            return ['message' => 'token error'];
        }

        request()->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        if ($toUser->balance - request('amount') < 0) {
            return ['message' => '餘額不足'];
        }

        return DB::transaction(function () use ($toUser) {
            $order = Order::create([
                'type' => '2',
                'to_user_id' => $toUser->id,
                'amount' => request('amount'),
            ]);

            $toUser->update(['balance' => $toUser->balance - request('amount')]);

            return ['order' => $order, 'to_user' => $toUser];
        });
    }
}
