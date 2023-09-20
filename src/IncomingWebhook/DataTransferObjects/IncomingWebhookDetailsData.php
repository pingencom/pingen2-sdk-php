<?php

declare(strict_types=1);

namespace Pingen\IncomingWebhook\DataTransferObjects;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

class IncomingWebhookDetailsData extends DataTransferObject
{
    public string $id;

    public string $type;

    public array $attributes;

    public ?IncomingWebhookRelationships $relationships;

    public ?ItemLinks $links;
}
