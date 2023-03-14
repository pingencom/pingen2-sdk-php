<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\General;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class RelationshipRelatedItemData
 * @package Pingen\DataTransferObjects\General
 */
class RelationshipRelatedItemData extends DataTransferObject
{
    public string $id;

    public string $type;
}
