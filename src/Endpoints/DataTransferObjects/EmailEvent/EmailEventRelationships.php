<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\EmailEvent;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class EmailEventRelationships
 * @package Pingen\Endpoints\DataTransferObjects\EmailEvent
 */
class EmailEventRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $email;
}
