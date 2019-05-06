<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type' => 'integer',
    ];

    public function from()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function to()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
