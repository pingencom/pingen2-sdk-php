<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\UserAssociation;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class UserAssociationsCollection
 * @package Pingen\DataTransferObjects\UserAssociation
 */
class UserAssociationsCollection extends DataTransferObject
{
    /**
     * @var \Pingen\Endpoints\DataTransferObjects\UserAssociation\UserAssociationsCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;

    /**
     * @var \Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationIncluded[]|null
     */
    public ?array $included;
}
