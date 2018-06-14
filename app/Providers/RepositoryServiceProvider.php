<?php

namespace App\Providers;

use App\Repositories\Finance\Reimburse\PayRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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
        $this->app->singleton('AuditRepository', \App\Repositories\Finance\Reimburse\AuditRepository::class);//报销审核
        $this->app->singleton('PayRepository', PayRepository::class);
    }
}
