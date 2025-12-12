<?php

declare(strict_types=1);

namespace Clinically\Smtp2GoTransport\Client;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;

class Smtp2GoApiClient
{
    private string $endpoint = '';

    private string $apiKey = '';

    public Client $client;

    public function __construct(array $config = [])
    {
        $this->endpoint = $config['endpoint'] ?? config('smtp2go.endpoint');
        throw_unless(filled($this->endpoint), InvalidArgumentException::class, 'SMTP2Go endpoint is required');

        $this->apiKey = $config['api_key'] ?? config('smtp2go.api_key');
        throw_unless(filled($this->apiKey), InvalidArgumentException::class, 'SMTP2Go API key is required');

        $this->client = new Client(
            [
                'base_uri' => rtrim($this->endpoint, '/').'/',
                'headers' => [
                    'X-Smtp2go-Api-Key' => $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]
        );
    }

    public function getAttachment(DataPart $attachment): array
    {
        return [
            'filename' => $attachment->getFilename(),
            'fileblob' => base64_encode($attachment->getBody()),
            'mimetype' => $attachment->getContentType(),
        ];
    }

    public function send(array $data): void
    {
        $sender = $this->getNameWithAddress($data['sender'][0]);
        $to = collect($data['to'])->map(fn ($addr) => $this->getNameWithAddress($addr))->all();
        $cc = collect($data['cc'] ?? [])->map(fn ($addr) => $this->getNameWithAddress($addr))->all();
        $bcc = collect($data['bcc'] ?? [])->map(fn ($addr) => $this->getNameWithAddress($addr))->all();
        $attachments = collect($data['attachments'])->map(fn ($att) => $this->getAttachment($att))->all();

        $payload = [
            'sender' => $sender,
            'to' => $to,
            'subject' => $data['subject'],
        ];

        if (! empty($cc)) {
            $payload['cc'] = $cc;
        }

        if (! empty($bcc)) {
            $payload['bcc'] = $bcc;
        }

        if (filled($data['htmlBody'])) {
            $payload['html_body'] = $data['htmlBody'];
        }

        if (filled($data['textBody'])) {
            $payload['text_body'] = $data['textBody'];
        }

        if (! empty($attachments)) {
            $payload['attachments'] = $attachments;
        }

        $this->client->post('email/send', [
            'json' => $payload,
        ]);
    }

    private function getNameWithAddress(Address $address): string
    {
        return $address->getName().' <'.$address->getAddress().'>';
    }
}
