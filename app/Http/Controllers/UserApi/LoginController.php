<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;
use Cookie;

class LoginController extends Controller
{

    public function userLogin()
    {
        $response = app('app.response');
        $response->header('P3P', 'CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        $cookie = Cookie::make('hk_auth_token', request()->input('hk_auth_token'), 60 * 24 * 365);
        return response($response->arrSuccess())->withHeaders($response->getHeaders())->withCookie($cookie);
    }

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

}
