<?php

namespace Amvphp\uploadzone;

class Common
{
    static $is_init = false;
    static $static_base = '';
    static $static_public = '';
    public function init()
    {
        if (true === self::$is_init) {
            return true;
        }
        self::$static_base = config('common.uploadzone_static_base');
        self::$static_public = config('common.uploadzone_static_public');
        array_map(function ($v) {
            \App\Classes\Utils\FrontBuilder::pushCss($v);
        }, []);
        array_map(function ($v) {
            \App\Classes\Utils\FrontBuilder::pushJs($v);
        }, []);
        return true;
    }
}
