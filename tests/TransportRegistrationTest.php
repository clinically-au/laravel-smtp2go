<?php

use Clinically\Smtp2GoTransport\Mail\Smtp2GoTransport;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    config()->set('mail.mailers.smtp2go', [
        'transport' => 'smtp2go',
        'endpoint' => 'https://api.smtp2go.com/v3',
        'api_key' => 'test-api-key',
    ]);
});

it('registers smtp2go transport', function () {
    $manager = Mail::getFacadeRoot();

    $transport = $manager->mailer('smtp2go')->getSymfonyTransport();

    expect($transport)->toBeInstanceOf(Smtp2GoTransport::class);
});

it('creates transport with config from mail.php', function () {
    config()->set('mail.mailers.smtp2go', [
        'transport' => 'smtp2go',
        'endpoint' => 'https://custom.endpoint.com',
        'api_key' => 'custom-key',
    ]);

    $manager = Mail::getFacadeRoot();
    $transport = $manager->mailer('smtp2go')->getSymfonyTransport();

    expect($transport)->toBeInstanceOf(Smtp2GoTransport::class);
});
