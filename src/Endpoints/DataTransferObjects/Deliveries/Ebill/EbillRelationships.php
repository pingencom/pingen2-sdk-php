<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill
 */
class EbillRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $organisation;
}
