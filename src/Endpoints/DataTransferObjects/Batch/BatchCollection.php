<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchCollection extends DataTransferObject
{
    /**
     * @var \Pingen\Endpoints\DataTransferObjects\Batch\BatchCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;

    /**
     * @var \Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationIncluded[]|null
     */
    public ?array $included;
}
