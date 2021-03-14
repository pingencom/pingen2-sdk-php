<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\UserAssociation;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject;

/**
 * Class UserAssociationDetailsData
 * @package Pingen\Models
 */
class UserAssociationDetailsData extends DataTransferObject
{
    public ?string $id;

    public ?string $type;

    public ?UserAssociationAttributes $attributes;

    public ?UserAssociationRelationships $relationships;

    public ?ItemLinks $links;
}
