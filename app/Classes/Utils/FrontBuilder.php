<?php

namespace App\Classes\Utils;
class FrontBuilder
{
    public static $css_all = [];
    public static $js_all = [];

    public static function pushCss($url)
    {
        if (empty(self::$css_all[$url])) {
            self::$css_all[$url] = $url;
        }
    }

    public static function pushJs($url)
    {
        if (empty(self::$js_all[$url])) {
            self::$js_all[$url] = $url;
        }
    }

    public static function showAllCss()
    {
        $content = '';
        foreach (self::$css_all as $item) {
            $content .= '<link  type="text/css" rel="stylesheet" href="' . $item . '">' . "\n";
        }
        self::$css_all = [];
        return $content;
    }

    public static function showAllJs()
    {
        $content = '';
        foreach (self::$js_all as $item) {
            $content .= '<script type="text/javascript" src="' . $item . '"></script>' . "\n";
        }
        self::$js_all = [];
        return $content;
    }
}