<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\EbillEvent;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class EbillEventRelationships
 * @package Pingen\Endpoints\DataTransferObjects\EbillEvent
 */
class EbillEventRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $ebill;
}
