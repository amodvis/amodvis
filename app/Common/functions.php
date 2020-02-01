<?php
// 全局函数
use App\Classes\Services\Module\ModuleService;

function getOriginEnv($key, $default = '')
{
    $ret = $_ENV[$key] ?? $default;
    // todo read db and add new func
    if (!$ret && $key === 'FRONT_VERSION') {
        $ret = request()->input('FRONT_VERSION');
    }
    if($ret==='false'){
        $ret = false;
    }elseif($ret === 'true'){
        $ret = true;
    }
    return $ret;
}

function getFrontVersion()
{
    return app(ModuleService::class)->getFrontVersion();
}
