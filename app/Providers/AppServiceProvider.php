<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        //
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

        $this->registerMorpMap();
    }


    /**
     * Register model morp map.
     *
     * @return void
     */
    protected function registerMorpMap()
    {
        $this->setMorphMap([
            'staff' => \App\Models\HR\Staff::class,
        ]);
    }

    /**
     * Set the morph map for polymorphic relations.
     *
     * @param array|null $map
     * @param bool $merge
     * @return array
     */
    protected function setMorphMap(array $map = null, bool $merge = true): array
    {
        return Relation::morphMap($map, $merge);
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
