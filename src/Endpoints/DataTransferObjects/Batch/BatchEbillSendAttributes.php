<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Exceptions\ValidationException;

/**
 * Attributes used to send a batch with channel type "ebill".
 * The delivery product is fixed and set by the constructor.
 *
 * @method BatchEbillSendAttributes setDeliveryProduct(string $value)
 */
class BatchEbillSendAttributes extends BatchChannelSendAttributes
{
    public const TYPE = 'batches_channel_ebill_send';

    public const DELIVERY_PRODUCT = 'electronic_ebill';

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
