<?php

namespace App\Providers;

use App\Models\Department;
use App\Observers\DepartmentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者加是否延迟加载.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Department::observe(DepartmentObserver::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('AttendanceService', \App\Services\App\AttendanceService::class);//考勤
        $this->app->singleton('AuditService', \App\Services\Finance\Reimburse\AuditService::class);//报销审核
    }

    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'AttendanceService',
            'AuditService',
        ];
    }
}
