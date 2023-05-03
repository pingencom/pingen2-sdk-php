<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\BatchEvent;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchEventRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $batch;
}
