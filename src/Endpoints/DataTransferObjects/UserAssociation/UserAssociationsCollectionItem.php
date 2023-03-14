<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\UserAssociation;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class UserAssociationItem
 * @package Pingen\Models
 */
class UserAssociationsCollectionItem extends DataTransferObject
{
    public string $type;

    public string $id;

    public UserAssociationAttributes $attributes;

    public ?ItemLinks $links;

    public ?UserAssociationRelationships $relationships;
}
