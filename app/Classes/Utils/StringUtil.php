<?php

namespace App\Classes\Utils;

class StringUtil
{

    public static function routeReplaceForTag($route_str)
    {
        $replace = time();
        return preg_replace('/:[a-zA-Z]+[0-9a-zA-Z_]+/', $replace, $route_str);
    }
}
