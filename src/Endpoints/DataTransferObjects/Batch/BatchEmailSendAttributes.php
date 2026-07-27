<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Exceptions\ValidationException;

/**
 * Attributes used to send a batch with channel type "email".
 * The delivery product is fixed and set by the constructor.
 *
 * @method BatchEmailSendAttributes setDeliveryProduct(string $value)
 */
class BatchEmailSendAttributes extends BatchChannelSendAttributes
{
    public const TYPE = 'batches_channel_email_send';

    public const DELIVERY_PRODUCT = 'electronic_email';

    protected string $delivery_product;

    public function __construct()
    {
        $this->setDeliveryProduct(self::DELIVERY_PRODUCT);
    }

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

        if ($this->delivery_product !== self::DELIVERY_PRODUCT) {
            throw new ValidationException(
                (string) json_encode([sprintf('The delivery_product field must be %s.', self::DELIVERY_PRODUCT)])
            );
        }
    }
}
