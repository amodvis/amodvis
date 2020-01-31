<?php

namespace App\Classes\Utils;

use Firebase\JWT\JWT;
use ES\Log\FluentLogger;

class HttpAuth
{
    public static function getAppKey()
    {
        $app_key = request()->header('app-key', '');
        if (!$app_key) {
            $app_key = request()->input('app_key', '');
        }
        return $app_key;
    }

    public static function getEcUserAuthHeader($app_key, $token): array
    {
        return ['appkey' => $app_key, 'token' => $token];
    }

    public static function getAmodAppVendorAuth($vendor_id, $app_name)
    {
        $secret = getOriginEnv('EC_APP_SECRET');
        $app_key = getOriginEnv('EC_APP_KEY');
        $time = time(); //当前时间
        $payload = [
            'iss' => 'amodvis', //签发者 可选
            'iat' => $time, //签发时间,
            'client_id' => $app_key,
            'data' => [
                // 自定义信息，不要定义敏感信息
                'vendor_id' => $vendor_id,
                'app_name' => $app_name,
            ],
            'scopes' => 'role_access',  //token标识，请求接口的token
            'exp' => $time + 3600 * 24 //
        ];
        $alg = 'HS256';
        return app(self::class)->encode($payload, $secret, $alg);
    }


    public static function getEcOpenAuthorization(): string
    {
        $secret = getOriginEnv('EC_APP_SECRET');
        $app_key = getOriginEnv('EC_APP_KEY');
        $time = time(); //当前时间
        $payload = [
            'iss' => 'amodvis', //签发者 可选
            'iat' => $time, //签发时间,
            'client_id' => $app_key,
            'data' => [
                // 自定义信息，不要定义敏感信息
            ],
            'scopes' => 'role_access',  //token标识，请求接口的token
            'exp' => $time + 600 // access_token过期时间,这里设置10分钟
        ];
        $alg = 'HS256';
        return app(self::class)->encode($payload, $secret, $alg);
    }


    public static function getAmodAuthorization($vendor_id): string
    {
        $secret = getOriginEnv('EC_APP_SECRET');
        $app_key = getOriginEnv('EC_APP_KEY');
        $time = time(); //当前时间
        $payload = [
            'iss' => 'amodvis', //签发者 可选
            'iat' => $time, //签发时间,
            'client_id' => $app_key,
            'data' => [
                // 自定义信息，不要定义敏感信息
                'vendor_id' => $vendor_id
            ],
            'scopes' => 'role_access',  //token标识，请求接口的token
            'exp' => $time + 60 * 24 * 365 // access_token过期时间,这里设置10分钟
        ];
        $alg = 'HS256';
        return app(self::class)->encode($payload, $secret, $alg);
    }

    public static function checkUserAllow($login_user_id, $user_id_in_db)
    {
        if (in_array($login_user_id, config('super_admin_user_id_list', []))) {
            return true;
        }
        if ($login_user_id == $user_id_in_db) {
            return true;
        }
        return false;
    }

    public static function encode($payload, $secret, $alg = ''): string
    {
        $now = time();
        $payload_config = [
            'iss' => isset($payload['iss']) ? $payload['iss'] : config('common.ec_app_auth.iss'),
            'exp' => isset($payload['exp']) ? $payload['exp'] : $now + (int)config('common.ec_app_auth.exp', 3600),
            'iat' => isset($payload['iat']) ? $payload['iat'] : $now
        ];
        $payload = array_merge($payload, $payload_config);
        $alg = empty($alg) ? config('common.ec_app_auth.alg') : $alg;
        $token = JWT::encode($payload, $secret, $alg);
        return $token;
    }

    public static function decode($token)
    {
        try {
            if (empty($token)) {
                $res = ['status' => false, 'code' => 1002, 'message' => 'Authorization不能为空',];
                throw new \Exception($res['message']);
            }
            $payload = json_decode(base64_decode((explode('.', $token))[1]), true);
            $app_key = data_get($payload, 'client_id', null);
            $secret = self::getSecretByAppKey($app_key);
            $decode_data = [];
            // 通过$app_key去查找对应的secret
            try {
                // 解析成功表示签名验证成功
                $decode_data = JWT::decode($token, $secret, array_keys(JWT::$supported_algs));
                $decode_data = UtilsCommon::objectToArray($decode_data);
            } catch (\Exception $e) {
                $res = ['message' => 'Authorization验证失败: ' . $e->getMessage()];
                FluentLogger::warning('common', $res);
            }
            return $decode_data;
        } catch (\Exception $e) {
            return [];
        }
    }

    public static function getSecretByAppKey($app_key)
    {
        $mapping = [
            getOriginEnv('EC_APP_KEY') => getOriginEnv('EC_APP_SECRET')
        ];
        FluentLogger::warning('common', ['message' => 'appkeymapping', 'mapping' => $mapping, 'key' => $app_key]);
        return $mapping[$app_key] ?? '';
    }
}
