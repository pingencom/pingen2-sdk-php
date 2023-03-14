<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\General;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class RelationshipRelatedManyLinksRelated
 * @package Pingen\DataTransferObjects\General
 */
class RelationshipRelatedManyLinksRelated extends DataTransferObject
{
    public ?string $href;

    public ?RelationshipRelatedManyLinksRelatedMeta $meta;
}
