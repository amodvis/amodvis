<?php

namespace Amvphp\sys_admin\example_app;

use App\Classes\Widget\Widget;
use App\Classes\Services\Media\MediaService;

class DefaultWidget extends Widget
{
    public function run($view)
    {
        return $this->view(__DIR__ . '/views', 'index', $view);
    }
}
