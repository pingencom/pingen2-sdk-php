<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\DataTransferObject;

/**
 * Class LetterPriceAttributes
 * @package Pingen\DataTransferObjects\Letter
 */
class LetterPriceAttributes extends DataTransferObject
{
    public string $currency;

    /**
     * @var float|int
     */
    public $price;
}
