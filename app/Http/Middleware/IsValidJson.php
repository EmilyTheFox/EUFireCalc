<?php

namespace App\Http\Middleware;

use Closure;

class IsValidJson
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
            json_decode($request->getContent());
            if (json_last_error() != JSON_ERROR_NONE) {
                return response()->json("Request JSON body isn't valid", 400);
            }
        }

        return $next($request);
    }
}