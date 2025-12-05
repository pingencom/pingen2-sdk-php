<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Email;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\Endpoints\DataTransferObjects\Deliveries\Email
 */
class EmailRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $organisation;
}
