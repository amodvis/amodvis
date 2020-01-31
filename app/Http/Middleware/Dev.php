<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App;

use Closure;

class Dev
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (App::environment(PROD)) {
            return response([
                'status' => false,
                'code' => 0,
                'message' => 'Forbidden',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
