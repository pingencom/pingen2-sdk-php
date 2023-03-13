<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Organisation;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class OrganisationDetailsData
 * @package Pingen\Models
 */
class OrganisationDetailsData extends DataTransferObject
{
    public string $id;

    public string $type;

    public OrganisationAttributes $attributes;

    public OrganisationRelationships $relationships;

    public ?ItemLinks $links;
}
