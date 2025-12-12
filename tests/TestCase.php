<?php

namespace Clinically\Smtp2GoTransport\Tests;

use Clinically\Smtp2GoTransport\Smtp2GoTransportServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            Smtp2GoTransportServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('smtp2go.endpoint', 'https://api.smtp2go.com/v3');
        config()->set('smtp2go.api_key', 'test-api-key');
    }
}
