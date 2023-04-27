<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\BatchEvent;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchEventCollectionItem extends DataTransferObject
{
    public string $type;

    public string $id;

    public BatchEventAttributes $attributes;

    public ?ItemLinks $links;

    public ?BatchEventRelationships $relationships;
}
