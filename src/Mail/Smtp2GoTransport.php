<?php

declare(strict_types=1);

namespace Clinically\Smtp2GoTransport\Mail;

use Clinically\Smtp2GoTransport\Client\Smtp2GoApiClient;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\MessageConverter;

class Smtp2GoTransport extends AbstractTransport
{
    /**
     * The last API response data (email_id, request_id).
     *
     * @var array{request_id: string, email_id: string}|null
     */
    protected ?array $lastResponse = null;

    public function __construct(protected Smtp2GoApiClient $client)
    {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {
        /** @var Message $originalMessage */
        $originalMessage = $message->getOriginalMessage();
        $email = MessageConverter::toEmail($originalMessage);

        // Forward any X-* custom headers to SMTP2GO
        $customHeaders = [];
        foreach ($email->getHeaders()->all() as $header) {
            $name = strtolower($header->getName());
            if (str_starts_with($name, 'x-')) {
                $customHeaders[] = [
                    'header' => $header->getName(),
                    'value' => $header->getBodyAsString(),
                ];
            }
        }

        $this->lastResponse = $this->client->send([
            'sender' => $email->getFrom(),
            'to' => $email->getTo(),
            'cc' => $email->getCc(),
            'bcc' => $email->getBcc(),
            'subject' => $email->getSubject(),
            'htmlBody' => $email->getHtmlBody(),
            'textBody' => $email->getTextBody(),
            'attachments' => $email->getAttachments(),
            'custom_headers' => $customHeaders,
        ]);

        // Store the email_id in a response header so downstream listeners
        // (e.g. MessageSent event) can access it for delivery tracking.
        if (! empty($this->lastResponse['email_id'])) {
            $originalMessage->getHeaders()->addTextHeader(
                'X-Smtp2go-Email-Id',
                $this->lastResponse['email_id'],
            );
        }
    }

    /**
     * Get the last API response data.
     *
     * @return array{request_id: string, email_id: string}|null
     */
    public function getLastResponse(): ?array
    {
        return $this->lastResponse;
    }

    /**
     * Get the string representation of the transport.
     */
    public function __toString(): string
    {
        return 'smtp2go';
    }
}
