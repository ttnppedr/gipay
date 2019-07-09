<?php

namespace App\Http\Middleware;

use Closure;

class PretreatmentRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $text = $this->processText($request['text']);
        $payee = $this->processPayee($text[0]);

        $request->merge([
            'who' => $text[0],
            'amount' => $text[1],
            'payee_id' => $payee[1],
            'payee_name' => $payee[2],
        ]);

        return $next($request);
    }

    private function processText($requestText)
    {
        $text = str_replace(',', ' ', $requestText);
        return preg_split('/\s+/', $text);
    }

    private function processPayee($who)
    {
        preg_match("/^<@(\w+)\|(\w+)>$/", $who, $payee);
        return $payee;
    }
}
