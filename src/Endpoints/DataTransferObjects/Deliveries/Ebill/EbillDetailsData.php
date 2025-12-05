<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Ebill
 */
class EbillDetailsData extends DataTransferObject
{
    public ?string $id;

    public string $type;

    public EbillAttributes $attributes;

    public ?ItemLinks $links;

    public ?EbillRelationships $relationships;
}
