<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

abstract class DeferredSingletonProvider extends ServiceProvider
{
    // 190812 TEMP
    protected $defer = false;

    /**
     * 业务服务提供者单例绑定
     * */
    protected $deferSingletons = [];

    public function register()
    {
        foreach ($this->deferSingletons as $abstract => $concrete) {
            $this->app->singleton($abstract, $concrete);
        }
    }

    public function provides()
    {
        return array_keys($this->deferSingletons);
    }

    final public function boot()
    {
        // do nothing due to defer
    }
}
