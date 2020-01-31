<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Classes\Utils\UtilsCommon;

/**
 * 过滤安全的输入
 * Class FrontShops
 * @package App\Http\Middleware
 */
class FilterSafeInput
{
    public function handle(Request $request, Closure $next)
    {
        UtilsCommon::frameGlobalSet('login_vendor_id', 0);
        UtilsCommon::frameGlobalSet('vendor_id_by_domain', 0);
        UtilsCommon::frameGlobalSet('auth_user_id', 0);
        return $next($request);
    }
}
