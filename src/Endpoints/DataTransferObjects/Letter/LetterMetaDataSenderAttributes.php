<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\Input;

/**
 * Class LetterMetaDataSenderAttributes
 * @package Pingen\DataTransferObjects\Letter
 *
 * @method LetterMetaDataSenderAttributes setName(string $value)
 * @method LetterMetaDataSenderAttributes setStreet(string $value)
 * @method LetterMetaDataSenderAttributes setNumber(string $value)
 * @method LetterMetaDataSenderAttributes setZip(string $value)
 * @method LetterMetaDataSenderAttributes setCity(string $value)
 * @method LetterMetaDataSenderAttributes setCountry(string $value)
 */
class LetterMetaDataSenderAttributes extends Input
{
    protected string $name;

    protected string $street;

    protected string $number;

    protected string $zip;

    protected string $city;

    protected string $country;
}
