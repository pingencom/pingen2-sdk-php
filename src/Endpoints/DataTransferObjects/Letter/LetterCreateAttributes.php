<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Exceptions\ValidationException;
use Pingen\Support\Input;

/**
 * Class LetterCreateAttributes
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
 * @method LetterCreateAttributes setMetaData(LetterMetaDataAttributes $metaData)
 */
class LetterCreateAttributes extends Input
{
    protected string $file_original_name;

    protected string $file_url;

    protected string $file_url_signature;

    protected string $address_position;

    protected bool $auto_send;

    protected ?string $delivery_product;

    protected ?string $print_mode;

    protected ?string $print_spectrum;

    protected ?LetterMetaDataAttributes $meta_data;

    /**
     * @param string[] $excludedParameters
     * @return void
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function validate(array $excludedParameters = []): void
    {
        parent::validate($excludedParameters);

        if ($this->auto_send) {
            $errorMsg = [];

            if (! isset($this->delivery_product)) {
                $errorMsg[] = 'When auto_send is set to true delivery_product field is required.';
            }

            if (! isset($this->print_mode)) {
                $errorMsg[] = 'When auto_send is set to true print_mode field is required.';
            }

            if (! isset($this->print_spectrum)) {
                $errorMsg[] = 'When auto_send is set to true print_spectrum field is required.';
            }

            if (count($errorMsg) > 0) {
                throw new ValidationException((string) json_encode($errorMsg));
            }
        }
    }
}
