<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider {

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
        $this->app->singleton('Dingtalk', \App\Services\Api\Dingtalk::class);
    }

    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides() {
        return [
            'Dingtalk',
        ];
    }

}
