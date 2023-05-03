<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Webhook;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

class WebhookCollectionItem extends DataTransferObject
{
    public string $type;

    public string $id;

    public WebhookAttributes $attributes;

    public ?ItemLinks $links;

    public ?WebhookRelationships $relationships;
}
