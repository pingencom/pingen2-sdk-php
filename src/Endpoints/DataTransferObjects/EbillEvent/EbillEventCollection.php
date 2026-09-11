<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\EbillEvent;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class EbillEventCollection
 * @package Pingen\Endpoints\DataTransferObjects\EbillEvent
 */
class EbillEventCollection extends DataTransferObject
{
    /**
     * @var \Pingen\Endpoints\DataTransferObjects\EbillEvent\EbillEventCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;

    public ?array $included;
}
