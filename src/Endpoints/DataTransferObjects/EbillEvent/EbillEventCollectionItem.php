<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\EbillEvent;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class EbillEventCollectionItem
 * @package Pingen\Endpoints\DataTransferObjects\EbillEvent
 */
class EbillEventCollectionItem extends DataTransferObject
{
    public string $type;

    public string $id;

    public EbillEventAttributes $attributes;

    public ?ItemLinks $links;

    public ?EbillEventRelationships $relationships;
}
