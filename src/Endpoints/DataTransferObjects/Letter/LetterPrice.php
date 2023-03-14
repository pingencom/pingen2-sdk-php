<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class LetterPrice
 * @package Pingen\DataTransferObjects\Letter
 */
class LetterPrice extends DataTransferObject
{
    public LetterPriceData $data;
}
