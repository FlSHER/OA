<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

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
    public function boot()
    {
        parent::boot();
        /* Model Start */

        //StaffTransfer
        \App\Models\HR\Attendance\StaffTransfer::creating(function ($model) {
            $model->onCreating();
        });
        \App\Models\HR\Attendance\StaffTransfer::saving(function ($model) {
            $model->onSaving();
        });
        //Attendance
        \App\Models\HR\Attendance\Attendance::saving(function ($model) {
            $model->onSaving();
        });

        /* Model End */
    }

}
