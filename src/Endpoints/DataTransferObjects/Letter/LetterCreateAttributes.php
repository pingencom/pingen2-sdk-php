<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\Input;

/**
 * Class LetterInputPOSTAttributes
 * @package Pingen\DataTransferObjects\Letter
 *
 * @method LetterCreateAttributes setFileUrl(string $value)
 * @method LetterCreateAttributes setFileUrlSignature(string $value)
 * @method LetterCreateAttributes setFileOriginalName(string $value)
 * @method LetterCreateAttributes setAddressPosition(string $value)
 * @method LetterCreateAttributes setAutoSend(bool $value)
 * @method LetterCreateAttributes setDeliveryProduct(string $value)
 * @method LetterCreateAttributes setPrintMode(string $value)
 * @method LetterCreateAttributes setPrintSpectrum(string $value)
 */
class LetterCreateAttributes extends Input
{
    protected string $file_original_name;

    protected string $file_url;

    protected string $file_url_signature;

    protected string $address_position;

    protected bool $auto_send;

    protected string $delivery_product;

    protected string $print_mode;

    protected string $print_spectrum;
}
