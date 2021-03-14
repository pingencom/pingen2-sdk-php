<?php

declare(strict_types=1);

namespace Pingen\Exceptions;

use Pingen\Support\DataTransferObject;

/**
 * Class JsonApiExceptionError
 * @package Pingen\Exceptions
 */
class JsonApiExceptionError extends DataTransferObject
{
    public string $code;

    public ?string $title;

    public ?string $details;

    public JsonApiExceptionErrorSource $source;
}
