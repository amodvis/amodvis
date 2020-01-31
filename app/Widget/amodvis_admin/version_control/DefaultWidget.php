<?php

namespace App\Widget\amodvis_admin\version_control;

use App\Classes\Widget\WidgetApp;
use App\Classes\Services\BackendWithModuleXml\Basic\AppService;

class DefaultWidget extends WidgetApp
{
    public function run($view)
    {
        return $this->view(__DIR__ . '/views', 'index', $view);
    }
}
