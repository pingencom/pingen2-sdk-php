<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Ebill
 */
class EbillCollectionItem extends DataTransferObject
{
    public string $type;

    public string $id;

    public EbillAttributes $attributes;

    public ?ItemLinks $links;

    public ?EbillRelationships $relationships;
}
