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

    public function show(User $user)
    {
        $token = request()->bearerToken();
        if (!$token || !Token::where('token', $token)->exists()) {
            return ['message' => 'token error'];
        }

        return $user;
    }

    public function block(User $user)
    {
        $token = request()->bearerToken();
        if (!$token || !Token::where('token', $token)->exists()) {
            return ['message' => 'token error'];
        }

        $user->update(['blocked' => true]);

        return $user->fresh();
    }

    public function unblock(User $user)
    {
        $token = request()->bearerToken();
        if (!$token || !Token::where('token', $token)->exists()) {
            return ['message' => 'token error'];
        }

        $user->update(['blocked' => false]);

        return $user->fresh();
    }

    public function indexOrder(User $user)
    {
        $token = request()->bearerToken();
        if (!$token || !Token::where('token', $token)->exists()) {
            return ['message' => 'token error'];
        }

        $row = request('row') ?? 10;

        return $user->orders()->with('to', 'from')->orderByDesc('created_at', 'desc')->paginate($row)->appends(['row' => $row]);
    }
}
