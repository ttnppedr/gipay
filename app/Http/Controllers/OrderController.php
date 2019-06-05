<?php

namespace App\Http\Controllers;

use App\Order;
use App\Token;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $token = request()->bearerToken();
        if (!$token || !Token::where('token', $token)->exists()) {
            return ['message' => 'token error'];
        }

        $row = request('row') ?? 20;
        $fromUserId = request('from_user_id') ?? null;
        $toUserId = request('to_user_id') ?? null;

        $appends = ['row' => $row];
        if ($fromUserId) {
            $appends['from_user_id'] = $fromUserId;
        }
        if ($toUserId) {
            $appends['to_user_id'] = $toUserId;
        }

        return Order::when($fromUserId, function ($query, $fromUserId) {
            return $query->where('from_user_id', $fromUserId);
        })->when($toUserId, function ($query, $toUserId) {
            return $query->where('to_user_id', $toUserId);
        })->orderBy('created_at', 'desc')->with('from', 'to')->paginate($row)->appends($appends);
    }
}
