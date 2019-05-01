<?php

namespace App\Http\Controllers;

use App\User;
use App\Token;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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
}
