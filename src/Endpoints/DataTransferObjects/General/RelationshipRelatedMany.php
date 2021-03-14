<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\General;

use Pingen\Support\DataTransferObject;

/**
 * Class RelationshipRelatedMany
 * @package Pingen\DataTransferObjects\General
 */
class RelationshipRelatedMany extends DataTransferObject
{
    public ?RelationshipRelatedManyLinks $links;
}
