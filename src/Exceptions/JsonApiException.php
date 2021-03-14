<?php

declare(strict_types=1);

namespace Pingen\Exceptions;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

/**
 * Class JsonApiException
 * @package Pingen\Exceptions
 */
class JsonApiException extends RequestException
{
    protected ExceptionBody $body;

    /**
     * Create a new exception instance.
     *
     * @param \Illuminate\Http\Client\Response $response
     * @return void
     */
    public function __construct(Response $response)
    {
        parent::__construct($response);

        $this->body = new ExceptionBody($response->json());
    }

    /**
     * @return ExceptionBody
     */
    public function getBody(): ExceptionBody
    {
        return $this->body;
    }
}
