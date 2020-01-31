<?php

namespace App\Widget\amodvis_admin\head_default;

use App\Classes\Widget\WidgetApp;
use App\Classes\Services\BackendWithModuleXml\Basic\AppService;

class DefaultWidget extends WidgetApp
{
    public function run($view)
    {
        $user_id = $view['login_vendor_id'];
        $page_options = ['page' => 1, 'page_size' => 50];
        $view['app_list_ret'] = app(AppService::class)->getList($user_id, $page_options);
        return $this->view(__DIR__ . '/views', 'index', $view);
    }
}