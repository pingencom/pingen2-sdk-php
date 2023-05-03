<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedMany;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $organisation;

    public RelationshipRelatedMany $events;
}
