<?php

namespace App\Http\Controllers\UserWeb;

use App\Http\Controllers\Controller;
use Cookie;

class LoginController extends Controller
{

    public function bindShopVendor()
    {
        $response = app('app.response');
        $response->header('P3P', 'CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        $cookie = Cookie::make('shop_vendor_token',
            request()->input('shop_vendor_token'), 60 * 24 * 365,
            null, null, null, false
        );
        return response($response->arrSuccess())->withHeaders($response->getHeaders())->withCookie($cookie);
    }

    public function userLogin()
    {
        $response = app('app.response');
        $response->header('P3P', 'CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        // 需要用户态的页面 尽量保持用户态的path以固定目录开头
        $paths = ['/cart'];
        $responses_ret = response($response->arrSuccess())->withHeaders($response->getHeaders());
        foreach ($paths as $path) {
            $cookie = Cookie::make('hk_auth_token',
                request()->input('hk_auth_token'), 60 * 24 * 365, $path
            );
            $responses_ret->withCookie($cookie);
        }
        $cookie = Cookie::make('hk_auth_token',
            null, null, '/'
        );
        $responses_ret->withCookie($cookie);
        $cookie = Cookie::make('hk_auth_token',
            null, null, '/index'
        );
        $responses_ret->withCookie($cookie);
        $cookie = Cookie::make('hk_auth_token',
            null, null, '/product'
        );
        $responses_ret->withCookie($cookie);
        return $responses_ret;
    }

}
