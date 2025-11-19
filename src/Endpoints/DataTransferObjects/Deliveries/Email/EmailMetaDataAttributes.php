<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Email;

use Pingen\Support\Input;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Email
 *
 * @method EmailMetaDataAttributes setSenderName(string $value)
 * @method EmailMetaDataAttributes setRecipientEmail(string $value)
 * @method EmailMetaDataAttributes setRecipientName(string $value)
 * @method EmailMetaDataAttributes setReplyEmail(string $value)
 * @method EmailMetaDataAttributes setReplyName(string $value)
 * @method EmailMetaDataAttributes setSubject(string $value)
 * @method EmailMetaDataAttributes setContent(string $value)
 */
class EmailMetaDataAttributes extends Input
{
    protected ?string $sender_name;

    protected ?string $recipient_email;

    protected ?string $recipient_name;

    protected ?string $reply_email;

    protected ?string $reply_name;

    protected ?string $subject;

    protected ?string $content;
}
