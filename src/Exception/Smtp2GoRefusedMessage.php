<?php

declare(strict_types=1);

namespace Clinically\Smtp2GoTransport\Exception;

use Symfony\Component\Mailer\Exception\TransportException;

final class Smtp2GoRefusedMessage extends TransportException
{
    /**
     * @param  list<string>  $failureCodes
     */
    public function __construct(
        string $message,
        public readonly int $acceptedCount,
        public readonly int $failedCount,
        public readonly array $failureCodes,
        public readonly string $requestId,
        public readonly string $emailId,
    ) {
        parent::__construct($message);
    }

    public function hasAcceptedRecipients(): bool
    {
        return $this->acceptedCount > 0;
    }
}
