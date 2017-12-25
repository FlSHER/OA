<?php

/**
 * 系统功能服务
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SystemServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * 权限
         */
        $this->app->singleton('Authority', \App\Services\AuthorityService::class);
        /**
         * 菜单
         */
        $this->app->singleton('Menu', \App\Services\MenuService::class);
        /**
         * 当前用户
         */
        $this->app->singleton('CurrentUser', \App\Services\CurrentUserService::class);
    }

}
