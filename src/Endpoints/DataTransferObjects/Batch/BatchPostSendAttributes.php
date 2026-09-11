<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Exceptions\ValidationException;

/**
 * Attributes used to send a batch with channel type "post".
 *
 * @method BatchPostSendAttributes setDeliveryProduct(string $value)
 * @method BatchPostSendAttributes setPrintMode(string $value)
 * @method BatchPostSendAttributes setPrintSpectrum(string $value)
 */
class BatchPostSendAttributes extends BatchChannelSendAttributes
{
    public const TYPE = 'batches_channel_post_send';

    public const DELIVERY_PRODUCTS = ['fast', 'cheap', 'bulk', 'premium', 'registered'];

    public const PRINT_MODES = ['simplex', 'duplex'];

    public const PRINT_SPECTRUMS = ['color', 'grayscale'];

    protected string $delivery_product;

    protected string $print_mode;

    protected string $print_spectrum;

    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @param string[] $excludedParameters
     * @return void
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function validate(array $excludedParameters = []): void
    {
        parent::validate($excludedParameters);

        $errorMsg = [];

        if (isset($this->delivery_product) && ! in_array($this->delivery_product, self::DELIVERY_PRODUCTS, true)) {
            $errorMsg[] = sprintf(
                'The delivery_product field must be one of: %s.',
                implode(', ', self::DELIVERY_PRODUCTS)
            );
        }

        if (isset($this->print_mode) && ! in_array($this->print_mode, self::PRINT_MODES, true)) {
            $errorMsg[] = sprintf('The print_mode field must be one of: %s.', implode(', ', self::PRINT_MODES));
        }

        if (isset($this->print_spectrum) && ! in_array($this->print_spectrum, self::PRINT_SPECTRUMS, true)) {
            $errorMsg[] = sprintf(
                'The print_spectrum field must be one of: %s.',
                implode(', ', self::PRINT_SPECTRUMS)
            );
        }

        if (count($errorMsg) > 0) {
            throw new ValidationException((string) json_encode($errorMsg));
        }
    }
}
