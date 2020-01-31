<?php

namespace App\Classes\Services\Cart;

use ES\Net\Http\FpfHttpClient;
use App\Classes\Utils\HttpAuth;

class CartService
{
    public function getUserCart($app_key, $token)
    {
        if (!$app_key || !$token) {
            return [];
        }
        $header = HttpAuth::getEcUserAuthHeader($app_key, $token);
        $http_client = app(FpfHttpClient::class);
        $api = getOriginEnv('EC_APP_INNER_BASE_URL') . 'api/cart/list';
        $get_arr = ['w' => 300, 'h' => 300];
        $http_option = [
            'headers' => $header
        ];
        $ret = $http_client->get($api, $get_arr, $http_option);
        $ret = json_decode($ret, true);
        if (empty($ret) || false == $ret['status']) {
            return [];
        }
        return $ret['data'];
    }

}
