<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\Input;

/**
 * Class LetterMetaDataRecipientAttributes
 * @package Pingen\DataTransferObjects\Letter
 *
 * @method LetterMetaDataRecipientAttributes setName(string $value)
 * @method LetterMetaDataRecipientAttributes setStreet(string $value)
 * @method LetterMetaDataRecipientAttributes setNumber(string $value)
 * @method LetterMetaDataRecipientAttributes setZip(string $value)
 * @method LetterMetaDataRecipientAttributes setCity(string $value)
 * @method LetterMetaDataRecipientAttributes setCountry(string $value)
 */
class LetterMetaDataRecipientAttributes extends Input
{
    protected string $name;

    protected string $street;

    protected string $number;

    protected string $zip;

    protected string $city;

    protected string $country;
}
