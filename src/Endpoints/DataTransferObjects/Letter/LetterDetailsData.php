<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class LetterDetailsData
 * @package Pingen\DataTransferObjects\Letter
 */
class LetterDetailsData extends DataTransferObject
{
    public ?string $id;

    public string $type;

    public LetterAttributes $attributes;

    public ?ItemLinks $links;

    public ?LetterRelationships $relationships;

    public ?LetterDetailsMeta $meta;
}
