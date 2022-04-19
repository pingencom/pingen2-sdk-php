<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Webhook;

use Pingen\Support\DataTransferObject;

/**
 * Class WebhookAttributes
 * @package Pingen\DataTransferObjects\Letter
 */
class WebhookAttributes extends DataTransferObject
{
    public string $event_category;

    public string $url;

    public string $signing_key;
}
