<?php

namespace App\Classes\Services\User;

use ES\Net\Http\FpfHttpClient;
use Cache;

class UserService
{
    const GET_USER_INFO_API = 'xxx';

    public function getLoginUserIdByToken($app_key, $token)
    {
        if (!$token || !$app_key) {
            return 0;
        }
        $key_prefix = 'common_auth-';
        $key = $key_prefix . md5($token);
        $user_id = 0;
        if ($token && $app_key) {
            $cache_data = Cache::get($key);
            $cache_data = false;
            if ($cache_data) {
                $row = json_decode($cache_data, true);
            } else {
                $row = $this->getUserInfo($token, $app_key);
                if (!empty($row)) {
                    Cache::put($key, json_encode($row), 3600 * 12);
                }
            }
            $user_id = $row['user_id'];
        }
        return $user_id;
    }

    public function getUserInfo($token, $app_key)
    {
        $api = getOriginEnv('EC_APP_INNER_BASE_URL') . self::GET_USER_INFO_API;
        $http_client = app(FpfHttpClient::class);
        $http_option = [
            'headers' => [
                'appkey' => $app_key,
                'token' => $token
            ]
        ];
        $get_arr = [];
        $ret = $http_client->get($api, $get_arr, $http_option);
        $ret = json_decode($ret, true);
        if (empty($ret) || false == $ret['status']) {
            return [];
        }
        return $ret['data'];
    }
}
