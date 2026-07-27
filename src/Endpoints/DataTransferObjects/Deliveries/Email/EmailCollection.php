<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Email;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Email
 */
class EmailCollection extends DataTransferObject
{
    /**
     * @var \Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;

    /**
     * @var \Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationIncluded[]|null
     */
    public ?array $included;
}
