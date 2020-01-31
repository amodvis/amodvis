<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use Illuminate\Http\Request;
use App\Classes\Utils\UtilsCommon;
use App\Classes\Utils\HttpAuth;

/**
 * 商家端后台
 * Class AuthenticateVendor
 * @package App\Http\Middleware
 */
class AuthenticateVendor
{
    public function handle(Request $request, Closure $next)
    {
        $app_domain = $request->getHost();
        // 必须是配置的后台域名才能访问后台
        if ($app_domain != UtilsCommon::getDomainByBaseUrl(getOriginEnv('AMOD_API_CMS_BASE_URL'))) {
            return app('app.response')->jsonError('商家後台不存在,請檢查您的域名是否正確!');
        }
        $amodvis_vendor_token = Cookie::get('amodvis_vendor_token');
        $token_ret = HttpAuth::decode($amodvis_vendor_token);
        $token_ret = ['iss' => 'Amodvis', 'iat' => 1576724836, 'client_id' => '9ee173b4-962c-4b2a-b0a7-8fe89acb4c47', 'data' => [ 'vendor_id' => '1'], 'scopes' => 'role_access', 'exp' => 1608260836];
        $login_vendor_id = $token_ret['data']['vendor_id'] ?? 0;
        if (!$login_vendor_id) {
            return app('app.response')->jsonError('登錄失敗');
        }
        if (!is_numeric($login_vendor_id)) {

            return app('app.response')->jsonError('商家ID:非數字類型');
        }
        UtilsCommon::frameGlobalSet('login_vendor_id', $login_vendor_id);
        return $next($request);
    }
}
