<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\EBill;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\EBill
 */
class EBillCollectionItem extends DataTransferObject
{
    public string $type;

    public string $id;

    public EBillAttributes $attributes;

    public ?ItemLinks $links;

    public ?EBillRelationships $relationships;
}
