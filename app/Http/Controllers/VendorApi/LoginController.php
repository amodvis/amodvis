<?php

/**
 * 商家后台SSO相关
 */

namespace App\Http\Controllers\VendorApi;

use App\Http\Controllers\Controller;
use Cookie;

class LoginController extends Controller
{
    public function login()
    {
        $response = app('app.response');
        $response->header('P3P', 'CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        $cookie = Cookie::make('amodvis_vendor_token', request()->input('amodvis_vendor_token'), 60 * 24 * 365);
        return response($response->arrSuccess())->withHeaders($response->getHeaders())->withCookie($cookie);
    }
}

