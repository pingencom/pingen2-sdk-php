<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Webhook;

use Pingen\Support\Input;

/**
 * @method WebhookCreateAttributes setEventCategory(string $value)
 * @method WebhookCreateAttributes setUrl(string $value)
 * @method WebhookCreateAttributes setSigningKey(string $value)
 */
class WebhookCreateAttributes extends Input
{
    protected string $event_category;

    protected string $url;

    protected string $signing_key;
}
