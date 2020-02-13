<?php

namespace Amvphp\amodvis_company\Chat;

use App\Classes\Widget\Widget;

class DefaultWidget extends Widget
{
    protected static $is_view_project = false;

    public function run($view)
    {
        $view['user_info'] = [
            "user_id" => 1,
            "user_name" => "user_1",
            "head_pic" => "http://106.54.93.177:9091/amodvis/static/image/fd/83/05/fd8305e6d8cad189dc342aa8ac8aa5a3.jpeg"
        ];
        return $view;
    }
}
