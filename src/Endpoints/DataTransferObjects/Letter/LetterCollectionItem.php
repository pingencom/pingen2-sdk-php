<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class LetterCollectionItem
 * @package Pingen\DataTransferObjects\Letter
 */
class LetterCollectionItem extends DataTransferObject
{
    public string $type;

    public string $id;

    public LetterAttributes $attributes;

    public ?ItemLinks $links;

    public ?LetterRelationships $relationships;
}
