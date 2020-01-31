<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        'login_vendor_id',
        'vendor_id_by_domain',
        'hk_auth_token',
        'amodvis_vendor_token',
    ];
}
