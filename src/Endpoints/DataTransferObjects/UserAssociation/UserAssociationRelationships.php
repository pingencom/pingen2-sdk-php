<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\UserAssociation;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Support\DataTransferObject;

/**
 * Class UserAssociationRelationships
 * @package Pingen\Models
 */
class UserAssociationRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $organisation;
}
