<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\EmailEvent;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class EmailEventCollection
 * @package Pingen\Endpoints\DataTransferObjects\EmailEvent
 */
class EmailEventCollection extends DataTransferObject
{
    /**
     * @var \Pingen\Endpoints\DataTransferObjects\EmailEvent\EmailEventCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;

    public ?array $included;
}
