<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject;

/**
 * Class LetterPriceData
 * @package Pingen\DataTransferObjects\Letter
 */
class LetterPriceData extends DataTransferObject
{
    public ?string $id;

    public string $type;

    public LetterPriceAttributes $attributes;

    public ?ItemLinks $links;
}
