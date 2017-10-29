<?php

/**
 * 工具类服务
 * create by Fisher 2016/8/27 <fisher9389@sina.com>
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ToolServiceProvider extends ServiceProvider
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
        /**
         * 加密
         */
        $this->app->singleton('Encyption', 'App\Services\EncyptionService');
        /**
         * curl
         */
        $this->app->bind('Curl', 'App\Services\CurlService');
        /**
         * 增删改查
         */
        $this->app->bind('App\Contracts\CURD', 'App\Contracts\CURD');
        $this->app->when('App\Http\Controllers\HR\StaffController')
            ->needs('App\Contracts\CURD')
            ->give('App\Services\Tools\CURDs\StaffCurdService');
        $this->app->when('App\Http\Controllers\HR\TransferController')
            ->needs('App\Contracts\CURD')
            ->give('App\Services\Tools\CURDs\TransferCurdService');
        /**
         * 操作日志
         */
        $this->app->bind('App\Contracts\OperationLog', 'App\Contracts\OperationLog');
        $this->app->when('App\Http\Controllers\HR\StaffController')
            ->needs('App\Contracts\OperationLog')
            ->give('App\Services\Tools\OperationLogs\StaffOperationLogService');
        /**
         * Excel导入
         */
        $this->app->bind('App\Contracts\ExcelImport', 'App\Contracts\ExcelImport');
        /**
         * Excel导出
         */
        $this->app->bind('App\Contracts\ExcelExport', 'App\Contracts\ExcelExport');
        /**
         * HRM系统工具服务
         */
        $this->app->singleton('HRM', 'App\Services\HRMService');
        /**
         * API响应
         */
        $this->app->singleton('ApiResponse', 'App\Services\ApiResponseService');
    }

    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'Encyption',
            'Curl',
            'App\Contracts\CURD',
            'App\Contracts\OperationLog',
            'App\Contracts\ExcelImport',
            'App\Contracts\ExcelExport',
            'HRM',
            'ApiResponse'
        ];
    }

}
