<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Classes\Utils\UtilsCommon;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $app_domain = request()->getHost();
        $app_port = request()->getPort();
        $origin = false;
        if (preg_match('/^[0-9\.]*$/', $app_domain)) {
            // IP访问
            $app_domain = 'http://' . $app_domain . ':' . $app_port . '/';
            $origin = true;
        }
        if (!$app_domain) {
            return true;
        }
        if ($app_domain === UtilsCommon::getDomainByBaseUrl(getOriginEnv('AMOD_API_BASE_URL'), $origin)) {
            // 前台API
            $this->mapUserApiRoutes();
        } elseif ($app_domain === UtilsCommon::getDomainByBaseUrl(getOriginEnv('AMOD_FRONT_BASE_URL'), $origin) ||
            true == $this->checkBindDomain($app_domain)) {
            // 前台WEB
            $this->mapUserWebRoutes();
        } elseif ($app_domain === UtilsCommon::getDomainByBaseUrl(getOriginEnv('AMOD_CMS_BASE_URL'), $origin)) {
            // 商家API与CMS，WEB域名共用
            $this->mapVendorWebRoutes();
            $this->mapVendorApiRoutes();
            $this->mapDevRoutes();
        }
    }

    /**
     * test route set
     * @return void
     */
    protected function mapDevRoutes()
    {
        Route::prefix('dev_tools')
            ->middleware('api')
            ->namespace($this->namespace . '\Dev')
            ->group(base_path('routes/dev.php'));
    }

    private function checkBindDomain($domain)
    {
        // todo add db
        $bindDomain = [
            '01infinity-mall.dd01.fun',
            '01infinity-mall.hktester.com',
            '01infinity-mall.hk01.com',
            'www.amodvis.com',
            'http://106.54.93.177:8091/'
        ];
        if (in_array($domain, $bindDomain)) {
            return true;
        }
        return false;
    }

    protected function mapUserWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace . '\\UserWeb')
            ->group(base_path('routes/user_web.php'));
    }

    protected function mapVendorWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace . '\\VendorWeb')
            ->group(base_path('routes/vendor_web.php'));
    }

    protected function mapUserApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace . '\\UserApi')
            ->group(base_path('routes/user_api.php'));
    }

    protected function mapVendorApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace . '\\VendorApi')
            ->group(base_path('routes/vendor_api.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
