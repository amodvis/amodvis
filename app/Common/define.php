<?php
// 常量定义
define('LOCAL', 'local');
define('DEV', 'dev');
define('STG', 'stg');
define('PROD', 'prod');
define('ALL_ENV', [LOCAL, DEV, STG, PROD]);
/*
|--------------------------------------------------------------------------
| 运行周期唯一值
|--------------------------------------------------------------------------
*/

// 尚未载入环境变量
define('UNIQUE', session_create_id());
