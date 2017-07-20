<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot() {
        parent::boot();
        /* Model Start */

        //Staff
        \App\Models\HR\Staff::saved(function($model) {
            $model->onSaved();
        });

        //StaffTransfer
        \App\Models\HR\StaffTransfer::saving(function($model) {
            $model->onSaving();
        });
        \App\Models\HR\StaffTransfer::saved(function($model) {
            $model->onSaved();
        });

        /* Model End */
    }

}
