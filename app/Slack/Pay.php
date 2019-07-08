<?php

namespace App\Slack;

use Illuminate\Http\Request;

class Pay
{
    protected $text;

    public function __construct(Request $request)
    {
        $this->text = explode(',', $request['text']);
    }

    public function getText()
    {
        return $this->text;
    }
}
