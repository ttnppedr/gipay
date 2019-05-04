<?php

namespace App\Http\Controllers;

use App\User;
use App\Token;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request, User $user)
    {
        $request->validate([
            'pw' => 'required|digits:4|confirmed',
        ]);

        $user->update(['password'=>$request->pw, 'blocked'=>false]);

        return 'Welcome to GIPay';
    }

    public function index()
    {
        $token = request()->bearerToken();
        if (!$token || !Token::where('token', $token)->exists()) {
            return ['message' => 'token error'];
        }

        $row = request('row') ?? 10;

        return User::paginate($row)->appends(['row' => $row]);
    }
}
