<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Organisation;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedMany;
use Pingen\Support\DataTransferObject;

/**
 * Class CompanyRelationships
 * @package Pingen\Models
 */
class OrganisationRelationships extends DataTransferObject
{
    public RelationshipRelatedMany $associations;

    public RelationshipRelatedMany $letters;
}
