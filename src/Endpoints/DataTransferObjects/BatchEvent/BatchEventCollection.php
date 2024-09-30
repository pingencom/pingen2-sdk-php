<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\BatchEvent;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchEventCollection extends DataTransferObject
{
    /**
     * @var \Pingen\Endpoints\DataTransferObjects\BatchEvent\BatchEventCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;

    /**
     * @var \Pingen\Endpoints\DataTransferObjects\Batch\BatchIncluded[]|null
     */
    public ?array $included;
}
