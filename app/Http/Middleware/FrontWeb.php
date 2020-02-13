<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use ES\Log\FluentLogger;
use Illuminate\Http\Request;
use App\Classes\Utils\UtilsCommon;
use App\Classes\Services\BackendWithModuleXml\Basic\AppService;
use App\Classes\Services\User\UserService;
use App\Classes\Utils\HttpAuth;

/**
 * 前台店铺
 * Class FrontShops
 * @package App\Http\Middleware
 */
class FrontWeb
{
    public function handle(Request $request, Closure $next)
    {
        $app_domain = $request->getHost();
        $app_port = request()->getPort();
        if (preg_match('/^[0-9\.]*$/', $app_domain)) {
            // IP访问
            $app_domain = $app_domain . ':' . $app_port;
        }
        $row = app(AppService::class)->getOneByDomain($app_domain);
        if (!empty($row)) {
            UtilsCommon::frameGlobalSet('vendor_id_by_domain', $row->user_id);
            UtilsCommon::frameGlobalSet('view_from', 'web');
            UtilsCommon::frameGlobalSet('app_key', $row->app_key);
            UtilsCommon::frameGlobalSet('app_name_by_domain', $row->app_name);
            $token = Cookie::get('hk_auth_token');
            UtilsCommon::frameGlobalSet('hk_auth_token', $token);
            $user_id = app(UserService::class)->getLoginUserIdByToken($row->app_key, $token);
            if ($user_id) {
                UtilsCommon::frameGlobalSet('auth_user_id', $user_id);
            }
            return $next($request);
        }
        // amodvis_app通过页面列表页注册的TOKEN
        $shop_vendor_token = Cookie::get('shop_vendor_token');
        if (!$shop_vendor_token) {
            return app('app.response')->jsonError('商家TOKEN信息不存在');
        }
        $token_ret = HttpAuth::decode($shop_vendor_token);
        $vendor_id_by_domain = $token_ret['data']['vendor_id'] ?? 0;
        $app_name_by_domain = $token_ret['data']['app_name'] ?? 0;
        if (!$vendor_id_by_domain) {
            return app('app.response')->jsonError('登錄異常:10001');
        }
        if (!$app_name_by_domain) {
            return app('app.response')->jsonError('登錄異常:10002');
        }
        UtilsCommon::frameGlobalSet('vendor_id_by_domain', $vendor_id_by_domain);
        UtilsCommon::frameGlobalSet('app_name_by_domain', $app_name_by_domain);
        return $next($request);
    }

}
