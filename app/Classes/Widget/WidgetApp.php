<?php

namespace App\Classes\Widget;

use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;
use Illuminate\Support\Facades\App;
use App\Classes\Services\Module\ModuleService;

abstract class WidgetApp
{
    public function init()
    {
    }

    public function view($view_path, $page, $view = [])
    {
        $this->changeViewDir($view_path);
        $html = view($page, $view)->render();
        $this->changeViewDir(dirname(__DIR__, 3) . '/resources/views');
        return $html;
    }

    public function changeViewDir($path)
    {
        $path = [$path];
        $finder = new FileViewFinder(App::make('files'), $path);
        View::setFinder($finder);;
    }

    public abstract function run($view);

    public static function widget($view = [])
    {
        $call_class = get_called_class();
        $instance = new $call_class;
        return $instance->run($view);
    }
}
