<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Support\Input;

/**
 * Sending a batch takes different attributes depending on the channel the batch
 * was created for. Every channel is represented by its own json api type.
 *
 * @package Pingen\Endpoints\DataTransferObjects\Batch
 */
abstract class BatchChannelSendAttributes extends Input
{
    /**
     * The json api type sent along with these attributes.
     *
     * @return string
     */
    abstract public function getType(): string;
}
