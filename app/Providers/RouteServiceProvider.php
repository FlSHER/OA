<?php

namespace App\Providers;

use App\Support\ParserIdentity;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

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

        // 注册中国大陆手机号码验证规则
        Validator::extend('cn_phone', function (...$parameters) {
            return (bool) preg_match('/^(\+?0?86\-?)?1[3-9]\d{9}$/', $parameters[1]);
        });

        // 注册身份证号码验证规则
        Validator::extend('ck_identity', function (...$parameters) {
            $parser = new ParserIdentity($parameters[1]);
            return $parser->isValidate();
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();

        $this->mapApiRoutes();

        $this->mapReimburseApiRoutes();//报销接口路由

        $this->mapDingtalkApiRoutes();//钉钉接口路由
        //
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
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
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
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }

    /**
     * 报销路由
     */
    protected function mapReimburseApiRoutes()
    {
        Route::prefix('reimburse')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api/reimburse.php'));
    }

    /**
     * 钉钉路由
     */
    protected function mapDingtalkApiRoutes()
    {
        Route::prefix('dingtalk')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api/dingtalk.php'));
    }
}
