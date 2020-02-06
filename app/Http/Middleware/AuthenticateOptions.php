<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateOptions
{
    public function handle(Request $request, Closure $next)
    {
        if ('OPTIONS' === $request->method()) {
            app('app.response')->header('Access-Control-Allow-Headers', 'access-control-allow-origin,authenticate,content-type,content-length,app-key,hk-auth-token,shop-vendor-token,cache-control');
        }
        $allow_list = getOriginEnv('ALLOW_OPTIONS_BASE_URL_LIST');
        $allow_list = explode(',', $allow_list);
        $origin = $request->header('origin');
        $config_options = config('common.allow_options_base_urls', []);
        if (in_array($origin, $allow_list) || in_array($origin, $config_options)) {
            app('app.response')->header('Access-Control-Allow-Origin', $origin);
        }
        app('app.response')->header('Access-Control-Allow-Credentials', 'true');
        app('app.response')->header('Access-Control-Allow-Methods', 'POST,GET,OPTIONS');
        if ('OPTIONS' === $request->method()) {
            return app('app.response')->jsonSuccess('');
        }
        return $next($request);
    }
}
