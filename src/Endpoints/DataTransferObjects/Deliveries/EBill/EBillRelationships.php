<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\EBill;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\Endpoints\DataTransferObjects\Deliveries\EBill
 */
class EBillRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $organisation;
}
