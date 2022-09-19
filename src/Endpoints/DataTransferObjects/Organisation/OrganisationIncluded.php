<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Organisation;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject;

/**
 * Class OrganisationIncluded
 * @package Pingen\Models
 */
class OrganisationIncluded extends DataTransferObject
{
    public string $id;

    public string $type;

    public OrganisationAttributes $attributes;

    public ItemLinks $links;
}
