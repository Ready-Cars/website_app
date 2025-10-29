<?php

namespace App\Providers;

use App\Mail\Transport\ZeptomailTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class ZeptomailServiceProvider extends ServiceProvider
{
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
        Mail::extend('zeptomail', function (array $config = []) {
            return new ZeptomailTransport(
                config('services.zeptomail.api_key')
            );
        });
    }
}
