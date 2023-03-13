<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\General;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class RelationshipRelatedItem
 * @package Pingen\DataTransferObjects\General
 */
class RelationshipRelatedItem extends DataTransferObject
{
    public RelationshipRelatedItemLinks $links;

    public RelationshipRelatedItemData $data;
}
