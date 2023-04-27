<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchDetailsData extends DataTransferObject
{
    public ?string $id;

    public string $type;

    public BatchAttributes $attributes;

    public ?ItemLinks $links;

    public ?BatchRelationships $relationships;

    public ?BatchDetailsMeta $meta;
}
