<?php

/**
 * 系统功能服务
 */

namespace App\Providers;

use App\Models\Department;
use App\Support\ParserIdentity;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\Relation;

class SystemServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Department::saving(function (Department $department) {
            $department->changeFullName();
        });

        Department::saved(function (Department $department) {
            $department->changeRoleAuthority();
        });

        // 注册中国大陆手机号码验证规则
        Validator::extend('cn_phone', function (...$parameters) {
            return (bool) preg_match('/^(\+?0?86\-?)?1[3-9]\d{9}$/', $parameters[1]);
        });

        // 注册身份证号码验证规则
        Validator::extend('ck_identity', function (...$parameters) {
            $parser = new ParserIdentity($parameters[1]);
            return $parser->isValidate();
        });

        $this->registerMorpMap();
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


    /**
     * Register model morp map.
     *
     * @return void
     */
    protected function registerMorpMap()
    {
        $this->setMorphMap([
            'staff' => \App\Models\HR\Staff::class,
            'shop' => \App\Models\HR\Shop::class,
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

}
