<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Support\Input;

/**
 * @method BatchSendAttributes setDeliveryProduct(array $value)
 * @method BatchSendAttributes setPrintMode(string $value)
 * @method BatchSendAttributes setPrintSpectrum(string $value)
 */
class BatchSendAttributes extends Input
{
    protected array $delivery_product;

    protected string $print_mode;

    protected string $print_spectrum;
}
