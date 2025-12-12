<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('transport extends AbstractTransport')
    ->expect('Clinically\Smtp2GoTransport\Mail\Smtp2GoTransport')
    ->toExtend('Symfony\Component\Mailer\Transport\AbstractTransport');

arch('client is in Client namespace')
    ->expect('Clinically\Smtp2GoTransport\Client')
    ->toOnlyBeUsedIn([
        'Clinically\Smtp2GoTransport\Mail',
        'Clinically\Smtp2GoTransport',
    ]);

arch('strict types are declared')
    ->expect('Clinically\Smtp2GoTransport')
    ->toUseStrictTypes();

arch('namespaces are correct')
    ->expect('Clinically\Smtp2GoTransport')
    ->toBeClasses()
    ->not->toBeAbstract()
    ->ignoring('Clinically\Smtp2GoTransport\Smtp2GoTransportServiceProvider');
