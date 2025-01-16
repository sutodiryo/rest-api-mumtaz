<?php

namespace App\Providers;

use App\Events\SuccessfulTransaction;
use App\Listeners\SendEmailToUser;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        SuccessfulTransaction::class => [
            SendEmailToUser::class,
        ]
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
