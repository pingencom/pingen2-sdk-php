<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Organisation;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class OrganisationCollection
 * @package Pingen\Endpoints\DataTransferObjects\Organisation
 */
class OrganisationCollection extends DataTransferObject
{
    /**
     * @var \Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;

    public ?array $included;
}
