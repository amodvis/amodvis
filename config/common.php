<?php
return [
    'http_options' => [
        'connect_timeout_ms' => env('HTTP_CONNECT_TIMEOUT_MS', 1000),
        'timeout_ms' => env('HTTP_TIMEOUT_MS', 1000),
    ],
    'amod_front_public_domain' => env('AMOD_FRONT_PUBLIC_BASE_URL'),
    'amod_api_cms_domain' => env('AMOD_API_CMS_BASE_URL'),
    'amod_api_domain' => env('AMOD_API_BASE_URL'),
    'module_fetch_url' => env('AMOD_API_BASE_URL') . 'api/module_api/index/get_modules_data/',
    'public_ice_dist_url' => 'stg' == env('APP_ENV') ? 'https://amodvis-react.hktester.com/' : env('REACT_BASE_URL'),
    'user_sso_url' => 'https://' . ($_SERVER['HTTP_HOST'] ?? '') . '/user/login/sso?hk_auth_token=',
    'amod_js_domain' => explode("://", trim(env('AMOD_API_CMS_BASE_URL'), '/'))[1],
    'uploadzone_static_base' => env('AMOD_FRONT_PUBLIC_BASE_URL') . 'laravle-amodvis/uploadzone/',
    'uploadzone_static_public' => env('AMOD_FRONT_PUBLIC_BASE_URL') . 'laravle-amodvis/uploadzone/public/',
    // 有权限获取所有商家的商品与文章信息的用户
    'super_admin_user_id_list' => explode(',', env('SUPER_ADMIN_USER_ID_LIST')),
    'important_vendor_list_for_item_query' => explode(',', env('IMPORTANT_VENDOR_LIST_FOR_ITEM_QUERY')),
    'ec_app_auth' => [
        'alg' => 'HS256',
        'exp' => 7200,
        'iss' => env('APP_NAME'),
    ],
    'aws_thumb' => [
        'url' => env('AWS_THUMB_URL'),
        'secret' => env('AWS_THUMB_SEC_SECRET'),
    ],
    'allow_options_base_urls' => [
        'http://localhost:4445',
        'http://localhost:4444',
        'https://amodvis.local.com',
        'https://amodvis-app.local.com',
        'https://www.amodvis.com',
        'https://app.amodvis.com',
        'https://admin.amodvis.com',
    ],
    'react_base_url' => env('REACT_BASE_URL')
];
