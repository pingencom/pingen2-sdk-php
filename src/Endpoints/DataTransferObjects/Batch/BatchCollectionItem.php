<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchCollectionItem extends DataTransferObject
{
    public string $type;

    public string $id;

    public BatchAttributes $attributes;

    public ?ItemLinks $links;

    public ?BatchRelationships $relationships;
}
