<?php
return [
    'cdn' => [
        // 对于开启了 cache.headers 中间件的接口，在不同环境下启用该中间件的域名
        'cacheHeadersHosts' => [
            LOCAL => [
                'amodvis-api.local.com',
                '01infinity-mall.hktester.com',
            ],
            DEV => [
                // DEV 暂未启用
            ],
            STG => [
                // forwarded to ec-general-backend.hktester.com
                // cp host is ec-control-panel.hktester.com
                'amodvis-api.hktester.com',
                '01infinity-mall.hktester.com',
            ],
            PROD => [
                // forwarded to ec-general-backend.wezeroplus.com
                // cp host is ec-control-panel.wezeroplus.com
                'amodvis-api.hk01.com',
                '01infinity-mall.hk01.com',
            ],
        ],
    ]
];
