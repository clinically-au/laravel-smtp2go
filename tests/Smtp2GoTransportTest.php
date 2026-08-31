<?php

use Clinically\Smtp2GoTransport\Client\Smtp2GoApiClient;
use Clinically\Smtp2GoTransport\Mail\Smtp2GoTransport;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Email;

function transportWithResponse(string $body): Smtp2GoTransport
{
    $apiClient = new Smtp2GoApiClient([
        'endpoint' => 'https://api.smtp2go.com/v3',
        'api_key' => 'test-key',
    ]);

    $apiClient->client = new Client([
        'handler' => HandlerStack::create(new MockHandler([new Response(200, [], $body)])),
        'base_uri' => 'https://api.smtp2go.com/v3/',
    ]);

    return new Smtp2GoTransport($apiClient);
}

function sampleEmail(): Email
{
    return (new Email)
        ->from('sender@example.com')
        ->to('recipient@example.com')
        ->subject('Test')
        ->html('<p>Body</p>');
}

it('adds the email id header when the message is accepted', function () {
    $transport = transportWithResponse(smtp2goApiResponse());

    // Symfony clones the message before sending, so read the header back off the sent copy.
    $sent = $transport->send(sampleEmail());

    expect($sent?->getOriginalMessage()->getHeaders()->get('X-Smtp2go-Email-Id')?->getBodyAsString())
        ->toBe('em_12345abcde')
        ->and($transport->getLastResponse()['email_id'])->toBe('em_12345abcde');
});

it('fails the send when SMTP2GO refuses the message', function () {
    $transport = transportWithResponse((string) json_encode([
        'request_id' => '7fb0e8c8-3820-4a77-b952-a060d6aa1ec4',
        'data' => [
            'succeeded' => 0,
            'failed' => 1,
            'failures' => ['From header sender domain not verified (staging.clinically.dev)'],
            'email_id' => '',
        ],
    ]));

    expect(fn () => $transport->send(sampleEmail()))
        ->toThrow(
            TransportException::class,
            'SMTP2GO did not accept the message for delivery (succeeded: 0, failed: 1): '
            .'From header sender domain not verified (staging.clinically.dev) '
            .'[request_id: 7fb0e8c8-3820-4a77-b952-a060d6aa1ec4]',
        );

    expect($transport->getLastResponse())->toBeNull();
});
