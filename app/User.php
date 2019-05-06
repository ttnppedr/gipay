<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'password'
    ];

    public function scopeSlack($query, $slack_id)
    {
        return $query->where('slack_id', '=', $slack_id);
    }

    public function from()
    {
        return $this->hasMany(Order::class, 'from_user_id');
    }

    public function to()
    {
        return $this->hasMany(Order::class, 'to_user_id');
    }

    public function orders()
    {
        return $this->from()->union($this->to());
    }
}
