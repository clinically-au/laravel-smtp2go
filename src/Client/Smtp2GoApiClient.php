<?php

declare(strict_types=1);

namespace Clinically\Smtp2GoTransport\Client;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Symfony\Component\Mailer\Exception\TransportException;
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

    /**
     * Send an email via the SMTP2GO API.
     *
     * Returns the API response data including `email_id` and `request_id`
     * which can be used for delivery tracking via webhooks. A message the API
     * accepted but refused to send raises a TransportException rather than
     * returning, so the failure surfaces through Laravel's mail stack.
     *
     * @param  array<string, mixed>  $data
     * @return array{request_id: string, email_id: string}
     *
     * @throws TransportException when SMTP2GO does not accept the message for delivery
     */
    public function send(array $data): array
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

        if (! empty($data['custom_headers'])) {
            $payload['custom_headers'] = $data['custom_headers'];
        }

        if (! empty($attachments)) {
            $payload['attachments'] = $attachments;
        }

        $response = $this->client->post('email/send', [
            'json' => $payload,
        ]);

        $body = json_decode($response->getBody()->getContents(), true);
        $body = is_array($body) ? $body : [];

        $responseBody = $body['data'] ?? null;
        $responseData = is_array($responseBody) ? $responseBody : [];

        $requestId = $this->asString($responseData['request_id'] ?? $body['request_id'] ?? '');
        $emailId = $this->asString($responseData['email_id'] ?? '');

        $this->guardAgainstRefusedSend($responseData, $requestId, $emailId);

        return [
            'request_id' => $requestId,
            'email_id' => $emailId,
        ];
    }

    /**
     * Fail loudly when SMTP2GO did not accept the message for delivery.
     *
     * The API answers HTTP 200 even when it refuses to send — an unverified sender
     * domain, a suspended account or an exceeded quota all come back as
     * `succeeded: 0, failed: 1` with the reason in `data.failures`. Anything short of
     * a fully accepted message is raised as a transport failure: Symfony's transport
     * contract is all-or-nothing, so a partially delivered message (`failed` > 0 with
     * some recipients accepted) has no way to be reported other than as a failure, and
     * reporting success would silently discard the recipients that were refused.
     *
     * @param  array<array-key, mixed>  $responseData
     *
     * @throws TransportException
     */
    private function guardAgainstRefusedSend(array $responseData, string $requestId, string $emailId): void
    {
        $succeeded = $this->asInt($responseData['succeeded'] ?? 0);
        $failed = $this->asInt($responseData['failed'] ?? 0);

        if ($failed === 0 && $succeeded > 0 && $emailId !== '') {
            return;
        }

        $rawFailures = $responseData['failures'] ?? [];
        $failures = collect(is_array($rawFailures) ? $rawFailures : [$rawFailures])
            ->map(fn ($failure) => $this->asString($failure))
            ->filter()
            ->implode('; ');

        throw new TransportException(sprintf(
            'SMTP2GO did not accept the message for delivery (succeeded: %d, failed: %d): %s [request_id: %s]',
            $succeeded,
            $failed,
            $failures !== '' ? $failures : 'no failure reason reported by the API',
            $requestId !== '' ? $requestId : 'unknown',
        ));
    }

    private function asString(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }

    private function asInt(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private function getNameWithAddress(Address $address): string
    {
        return $address->getName().' <'.$address->getAddress().'>';
    }
}
