<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Illuminate\Http\Request;
use App\Classes\Services\User\UserService;
use App\Classes\Utils\HttpAuth;
use App\Classes\Utils\UtilsCommon;
use Illuminate\Support\Facades\Cookie;

class AuthenticateUserCommon
{
    // user_api_not_force_auth
    const GET_USER_INFO_API = 'api/1.0/web/1.0/user/user/not_force_auth_info';

    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('hk-auth-token', '');
        $app_key = $request->header('app-key', '');
        $user_id = app(UserService::class)->getLoginUserIdByToken($app_key, $token);
        $ret = $this->getVendorIdByAuth($request);
        if (is_array($ret)) {
            return app('app.response')->jsonError($ret['message']);
        }
        $vendor_id = $ret;
        UtilsCommon::frameGlobalSet('shop_vendor_id', $vendor_id);
        UtilsCommon::frameGlobalSet('auth_user_id', $user_id);
        return $next($request);
    }

    private function getVendorIdByAuth($request)
    {
        $vendor_id = 0;
        $authenticate = $request->header('shop-vendor-token', '');
        if (!$authenticate) {
            // 后台演示接口走的cookie传token,其他请求走head传token
            $authenticate = Cookie::get('shop_vendor_token');
        }
        if ($authenticate) {
            $vendor_id = $this->convertAuthenticate($authenticate);
            if (!$vendor_id) {
                return app('app.response')->arrFail('auth error');
            }
            if (!is_numeric($vendor_id)) {
                return app('app.response')->arrFail('user_id is_numeric error');
            }
        } else {
            if (App::environment(DEV) || App::environment(LOCAL)) {
                $vendor_id = $request->input('shop_vendor_id');
            }
        }
        return $vendor_id;
    }

    /**
     * 需实现自己的鉴权逻辑
     * @param $authenticate
     * @return mixed
     */
    private function convertAuthenticate($authenticate)
    {
        $token_ret = HttpAuth::decode($authenticate);
        return $token_ret['data']['vendor_id'] ?? 0;
    }
}
