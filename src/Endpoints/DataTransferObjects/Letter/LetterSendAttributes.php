<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\Input;

/**
 * Class LetterSendAttributes
 * @package Pingen\DataTransferObjects\Letter
 *
 * @method LetterSendAttributes setDeliveryProduct(string $value)
 * @method LetterSendAttributes setPrintMode(string $value)
 * @method LetterSendAttributes setPrintSpectrum(string $value)
 */
class LetterSendAttributes extends Input
{
    protected string $delivery_product;

    protected string $print_mode;

    protected string $print_spectrum;
}
