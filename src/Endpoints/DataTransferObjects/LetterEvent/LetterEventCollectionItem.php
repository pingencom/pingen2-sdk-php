<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\LetterEvent;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class LetterEventCollectionItem
 * @package Pingen\Endpoints\DataTransferObjects\Letter
 */
class LetterEventCollectionItem extends DataTransferObject
{
    public string $type;

    public string $id;

    public LetterEventAttributes $attributes;

    public ?ItemLinks $links;

    public ?LetterEventRelationships $relationships;
}
