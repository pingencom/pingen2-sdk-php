<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\General;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class RelationshipRelatedManyLinks
 * @package Pingen\DataTransferObjects\General
 */
class RelationshipRelatedManyLinks extends DataTransferObject
{
    public ?RelationshipRelatedManyLinksRelated $related;
}
