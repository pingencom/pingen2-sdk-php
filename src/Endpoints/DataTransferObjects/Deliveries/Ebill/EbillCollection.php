<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationIncluded;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Ebill
 */
class EbillCollection extends DataTransferObject
{
    /**
     * @var EbillCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;

    /**
     * @var OrganisationIncluded[]|null
     */
    public ?array $included;
}
