<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationIncluded;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchCollection extends DataTransferObject
{
    /**
     * @var BatchCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;

    /**
     * @var OrganisationIncluded[]
     */
    public ?array $included;
}
