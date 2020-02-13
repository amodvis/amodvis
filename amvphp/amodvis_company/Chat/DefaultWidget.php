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
            "head_pic" => "https://static-upload.local.com/amodvis/static/image/14/9c/ff/149cffa10522fed6855612a647924663.jpeg"
        ];
        return $view;
    }
}
