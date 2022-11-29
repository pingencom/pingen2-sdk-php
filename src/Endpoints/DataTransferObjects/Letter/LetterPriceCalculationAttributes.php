<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\Input;

/**
 * Class LetterPriceCalculationAttributes
 * @package Pingen\DataTransferObjects\Letter
 *
 * @method LetterPriceCalculationAttributes setCountry(string $value)
 * @method LetterPriceCalculationAttributes setDeliveryProduct(string $value)
 * @method LetterPriceCalculationAttributes setPrintMode(string $value)
 * @method LetterPriceCalculationAttributes setPrintSpectrum(string $value)
 * @method LetterPriceCalculationAttributes setPaperTypes(array $value)
 */
class LetterPriceCalculationAttributes extends Input
{
    protected string $country;

    protected string $print_mode;

    protected string $print_spectrum;

    protected string $delivery_product;

    protected array $paper_types;
}
