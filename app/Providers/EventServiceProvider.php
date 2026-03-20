<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\UpdateUserActiveSession;
use App\Listeners\ClearUserActiveSession;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\SPK;
use App\Observers\SpkObserver;
use App\Models\SpkItemCetakLog;
use App\Observers\SpkItemCetakLogObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            UpdateUserActiveSession::class,
        ],
        Logout::class => [
            ClearUserActiveSession::class,
        ],
    
    ];

    /**
     * The event observers mappings for the application.
     *
     * @var array
     */
    protected $observers = [
        SPK::class => [SpkObserver::class],
        SpkItemCetakLog::class => [SpkItemCetakLogObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
