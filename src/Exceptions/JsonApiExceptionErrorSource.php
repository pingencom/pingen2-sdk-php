<?php

declare(strict_types=1);

namespace Pingen\Exceptions;

use Pingen\Support\DataTransferObject;

/**
 * Class JsonApiExceptionErrorSource
 * @package Pingen\Exceptions
 */
class JsonApiExceptionErrorSource extends DataTransferObject
{
    public ?string $pointer;

    public ?string $parameter;
}
