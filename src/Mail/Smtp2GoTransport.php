<?php

declare(strict_types=1);

namespace Clinically\Smtp2GoTransport\Mail;

use Clinically\Smtp2GoTransport\Client\Smtp2GoApiClient;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class Smtp2GoTransport extends AbstractTransport
{
    public function __construct(protected Smtp2GoApiClient $client)
    {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(\Symfony\Component\Mailer\SentMessage $message): void
    {
        /** @var \Symfony\Component\Mime\Message $originalMessage */
        $originalMessage = $message->getOriginalMessage();
        $email = MessageConverter::toEmail($originalMessage);

        $this->client->send([
            'sender' => $email->getFrom(),
            'to' => $email->getTo(),
            'cc' => $email->getCc(),
            'bcc' => $email->getBcc(),
            'subject' => $email->getSubject(),
            'htmlBody' => $email->getHtmlBody(),
            'textBody' => $email->getTextBody(),
            'attachments' => $email->getAttachments(),
        ]);
    }

    /**
     * Get the string representation of the transport.
     */
    public function __toString(): string
    {
        return 'smtp2go';
    }
}
