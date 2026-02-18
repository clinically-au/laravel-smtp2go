<?php

use Clinically\Smtp2GoTransport\Client\Smtp2GoApiClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;

function smtp2goApiResponse(array $overrides = []): string
{
    return json_encode(array_merge([
        'request_id' => 'aa253463-0c0e-fake-a]e8-ce0ac3c3e553',
        'data' => [
            'succeeded' => 1,
            'failed' => 0,
            'failures' => [],
            'email_id' => 'em_12345abcde',
            'request_id' => 'aa253463-0c0e-fake-a]e8-ce0ac3c3e553',
        ],
    ], $overrides));
}

beforeEach(function () {
    $this->requestHistory = [];
    $this->mock = new MockHandler([
        new Response(200, [], smtp2goApiResponse()),
    ]);

    $handlerStack = HandlerStack::create($this->mock);
    $handlerStack->push(Middleware::history($this->requestHistory));

    $this->client = new Smtp2GoApiClient([
        'endpoint' => 'https://api.smtp2go.com/v3',
        'api_key' => 'test-key',
    ]);

    $this->client->client = new Client([
        'handler' => $handlerStack,
        'base_uri' => 'https://api.smtp2go.com/v3/',
        'headers' => [
            'X-Smtp2go-Api-Key' => 'test-key',
            'Accept' => 'application/json',
        ],
    ]);
});

it('constructs payload with sender and recipients', function () {
    $this->client->send([
        'sender' => [new Address('john.doe@example.com', 'John Doe')],
        'to' => [
            new Address('jane.smith@example.com', 'Jane Smith'),
            new Address('bob.johnson@example.com', 'Bob Johnson'),
        ],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test Subject',
        'htmlBody' => '<p>Test HTML</p>',
        'textBody' => 'Test Text',
        'attachments' => [],
    ]);

    expect($this->requestHistory)->toHaveCount(1);

    $request = $this->requestHistory[0]['request'];
    $body = json_decode($request->getBody()->getContents(), true);

    expect($body['sender'])->toBe('John Doe <john.doe@example.com>');
    expect($body['to'])->toBe([
        'Jane Smith <jane.smith@example.com>',
        'Bob Johnson <bob.johnson@example.com>',
    ]);
    expect($body['subject'])->toBe('Test Subject');
    expect($body['html_body'])->toBe('<p>Test HTML</p>');
    expect($body['text_body'])->toBe('Test Text');
});

it('includes cc and bcc when provided', function () {
    $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [new Address('cc@example.com', 'CC User')],
        'bcc' => [new Address('bcc@example.com', 'BCC User')],
        'subject' => 'Test',
        'htmlBody' => 'Body',
        'textBody' => null,
        'attachments' => [],
    ]);

    $request = $this->requestHistory[0]['request'];
    $body = json_decode($request->getBody()->getContents(), true);

    expect($body['cc'])->toBe(['CC User <cc@example.com>']);
    expect($body['bcc'])->toBe(['BCC User <bcc@example.com>']);
});

it('omits cc and bcc when empty', function () {
    $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => 'Body',
        'textBody' => null,
        'attachments' => [],
    ]);

    $request = $this->requestHistory[0]['request'];
    $body = json_decode($request->getBody()->getContents(), true);

    expect($body)->not->toHaveKey('cc');
    expect($body)->not->toHaveKey('bcc');
});

it('formats email addresses with space before angle bracket', function () {
    $this->client->send([
        'sender' => [new Address('sender@example.com', 'Test User')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => 'Body',
        'textBody' => null,
        'attachments' => [],
    ]);

    $request = $this->requestHistory[0]['request'];
    $body = json_decode($request->getBody()->getContents(), true);

    expect($body['sender'])->toBe('Test User <sender@example.com>');
    expect($body['to'][0])->toBe('Recipient <recipient@example.com>');
});

it('base64 encodes attachment fileblob', function () {
    $attachment = new DataPart('test file content', 'test.txt', 'text/plain');

    $formatted = $this->client->getAttachment($attachment);

    expect($formatted['filename'])->toBe('test.txt');
    expect($formatted['fileblob'])->toBe(base64_encode('test file content'));
    expect($formatted['mimetype'])->toBe('text/plain');
});

it('includes attachments in payload when provided', function () {
    $attachment = new DataPart('file content', 'document.pdf', 'application/pdf');

    $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => 'Body',
        'textBody' => null,
        'attachments' => [$attachment],
    ]);

    $request = $this->requestHistory[0]['request'];
    $body = json_decode($request->getBody()->getContents(), true);

    expect($body['attachments'])->toHaveCount(1);
    expect($body['attachments'][0]['filename'])->toBe('document.pdf');
    expect($body['attachments'][0]['fileblob'])->toBe(base64_encode('file content'));
    expect($body['attachments'][0]['mimetype'])->toBe('application/pdf');
});

it('omits html_body when not provided', function () {
    $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => null,
        'textBody' => 'Plain text only',
        'attachments' => [],
    ]);

    $request = $this->requestHistory[0]['request'];
    $body = json_decode($request->getBody()->getContents(), true);

    expect($body)->not->toHaveKey('html_body');
    expect($body['text_body'])->toBe('Plain text only');
});

it('omits text_body when not provided', function () {
    $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => '<p>HTML only</p>',
        'textBody' => null,
        'attachments' => [],
    ]);

    $request = $this->requestHistory[0]['request'];
    $body = json_decode($request->getBody()->getContents(), true);

    expect($body)->not->toHaveKey('text_body');
    expect($body['html_body'])->toBe('<p>HTML only</p>');
});

it('sends request to correct endpoint', function () {
    $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => 'Body',
        'textBody' => null,
        'attachments' => [],
    ]);

    $request = $this->requestHistory[0]['request'];

    expect((string) $request->getUri())->toBe('https://api.smtp2go.com/v3/email/send');
    expect($request->getMethod())->toBe('POST');
});

it('includes api key in headers', function () {
    $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => 'Body',
        'textBody' => null,
        'attachments' => [],
    ]);

    $request = $this->requestHistory[0]['request'];

    expect($request->getHeader('X-Smtp2go-Api-Key'))->toBe(['test-key']);
    expect($request->getHeader('Accept'))->toBe(['application/json']);
});

it('returns email_id and request_id from API response', function () {
    $result = $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => 'Body',
        'textBody' => null,
        'attachments' => [],
    ]);

    expect($result)->toBeArray()
        ->and($result['email_id'])->toBe('em_12345abcde')
        ->and($result['request_id'])->toBe('aa253463-0c0e-fake-a]e8-ce0ac3c3e553');
});

it('includes custom_headers in payload when provided', function () {
    $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => 'Body',
        'textBody' => null,
        'attachments' => [],
        'custom_headers' => [
            ['header' => 'X-Communication-Id', 'value' => '42'],
            ['header' => 'X-Tenant-Id', 'value' => 'test-tenant'],
        ],
    ]);

    $request = $this->requestHistory[0]['request'];
    $body = json_decode($request->getBody()->getContents(), true);

    expect($body['custom_headers'])->toBe([
        ['header' => 'X-Communication-Id', 'value' => '42'],
        ['header' => 'X-Tenant-Id', 'value' => 'test-tenant'],
    ]);
});

it('omits custom_headers when empty', function () {
    $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => 'Body',
        'textBody' => null,
        'attachments' => [],
        'custom_headers' => [],
    ]);

    $request = $this->requestHistory[0]['request'];
    $body = json_decode($request->getBody()->getContents(), true);

    expect($body)->not->toHaveKey('custom_headers');
});

it('returns empty strings when API response lacks expected fields', function () {
    // Replace the mock to return a minimal response
    $this->mock->reset();
    $this->mock->append(
        new Response(200, [], json_encode(['request_id' => 'req-123', 'data' => []])),
    );

    $result = $this->client->send([
        'sender' => [new Address('sender@example.com', 'Sender')],
        'to' => [new Address('recipient@example.com', 'Recipient')],
        'cc' => [],
        'bcc' => [],
        'subject' => 'Test',
        'htmlBody' => 'Body',
        'textBody' => null,
        'attachments' => [],
    ]);

    expect($result['email_id'])->toBe('')
        ->and($result['request_id'])->toBe('req-123');
});
