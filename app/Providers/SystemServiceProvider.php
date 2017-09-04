<?php

/**
 * 系统功能服务
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthorityService;
use App\Services\MenuService;
use App\Services\CurrentUserService;

class SystemServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        /**
         * 权限
         */
        $this->app->instance('Authority', new AuthorityService);
        /**
         * 菜单
         */
        $this->app->instance('Menu', new MenuService);
        /**
         * 当前用户
         */
        $this->app->singleton('CurrentUser', CurrentUserService::class);
    }

}
