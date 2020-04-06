<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;

class JWTMiddleware
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
        try
        {
            $user = JWTAuth::toUser($request->input('token'));
        }
        catch (Exception $e)
        {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
                return response()->json(['error' => 'Your token is invalid'], 500);
            else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
                return response()->json(['error' => 'Your token has expired. Please login again.'], 500);
            else
                return response()->json(['error' => 'Something is wrong. Please login again.'], 500);
        }
        return $next($request);
    }
}
