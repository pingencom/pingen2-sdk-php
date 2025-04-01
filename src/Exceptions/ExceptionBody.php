<?php

declare(strict_types=1);

namespace Pingen\Exceptions;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class ExceptionBody
 * @package Pingen\Exceptions
 */
class ExceptionBody extends DataTransferObject
{
    /**
     * @var \Pingen\Exceptions\JsonApiExceptionError[]
     */
    public array $errors;
}
