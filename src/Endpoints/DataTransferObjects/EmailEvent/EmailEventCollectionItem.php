<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\EmailEvent;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class EmailEventCollectionItem
 * @package Pingen\Endpoints\DataTransferObjects\EmailEvent
 */
class EmailEventCollectionItem extends DataTransferObject
{
    public string $type;

    public string $id;

    public EmailEventAttributes $attributes;

    public ?ItemLinks $links;

    public ?EmailEventRelationships $relationships;
}
