<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject;

/**
 * Class LetterIncluded
 * @package Pingen\Endpoints\DataTransferObjects\Letter
 */
class LetterIncluded extends DataTransferObject
{
    public string $id;

    public string $type;

    public LetterAttributes $attributes;

    public ItemLinks $links;
}
