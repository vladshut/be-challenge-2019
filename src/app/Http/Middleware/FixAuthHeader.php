<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FixAuthHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->hasHeader('Authorization')) {
            $authHeader = $request->header('Authorization');
            $authHeader = str_replace('Basic', 'Bearer', $authHeader);
            $request->headers->set('Authorization', $authHeader);
        }
        return $next($request);
    }
}
