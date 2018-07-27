<?php

declare(strict_types=1);

namespace Fisher\Amap\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\ManageRepository;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(
            $this->app->make('path.amap').'/router.php'
        );
    }

    /**
     * Regoster the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Publish admin menu.
        $this->app->make(ManageRepository::class)->loadManageFrom('amap', 'amap:admin-home', [
            'route' => true,
            'icon' => '📦',
        ]);
    }
}
