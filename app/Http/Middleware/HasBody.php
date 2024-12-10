<?php

namespace App\Http\Middleware;

use Closure;

class HasBody
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT')) {
            if (strlen($request->getContent()) === 0) {
                return response()->json("Request doesn't have a body", 400);
            }
        }

        return $next($request);
    }
}