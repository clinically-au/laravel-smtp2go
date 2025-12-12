<?php

declare(strict_types=1);

namespace Clinically\Smtp2GoTransport;

use Clinically\Smtp2GoTransport\Client\Smtp2GoApiClient;
use Clinically\Smtp2GoTransport\Mail\Smtp2GoTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class Smtp2GoTransportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/smtp2go.php', 'smtp2go');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/smtp2go.php' => config_path('smtp2go.php'),
            ], 'smtp2go-config');
        }

        Mail::extend('smtp2go', function (array $config = []) {
            $client = new Smtp2GoApiClient($config);

            return new Smtp2GoTransport($client);
        });
    }
}
