<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\LetterEvent;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Support\DataTransferObject;

/**
 * Class LetterEventCollection
 * @package Pingen\Endpoints\DataTransferObjects\LetterEvent
 */
class LetterEventCollection extends DataTransferObject
{
    /**
     * @var \Pingen\Endpoints\DataTransferObjects\LetterEvent\LetterEventCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;

    /**
     * @var \Pingen\Endpoints\DataTransferObjects\Letter\LetterIncluded[]|null
     */
    public ?array $included;
}
