<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\EBill;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\EBill
 */
class EBillDetailsData extends DataTransferObject
{
    public ?string $id;

    public string $type;

    public EBillAttributes $attributes;

    public ?ItemLinks $links;

    public ?EBillRelationships $relationships;
}
