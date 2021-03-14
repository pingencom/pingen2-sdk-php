<?php

declare(strict_types=1);

namespace Pingen\Exceptions;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

/**
 * Class RateLimitJsonApiException
 * @package Pingen\Exceptions
 */
class RateLimitJsonApiException extends JsonApiException
{
    public int $xRateLimitLimit;

    public int $xRateLimitRemaining;

    public int $retryAfter;

    public int $xRateLimitReset;

    /**
     * Create a new exception instance.
     *
     * @param \Illuminate\Http\Client\Response $response
     * @return void
     */
    public function __construct(Response $response)
    {
        parent::__construct($response);

        $this->xRateLimitLimit = (int) Arr::get($response->headers(), 'X-RateLimit-Limit');
        $this->xRateLimitRemaining = (int) Arr::get($response->headers(), 'X-RateLimit-Remaining');
        $this->retryAfter = (int) Arr::get($response->headers(), 'Retry-After');
        $this->xRateLimitReset = (int) Arr::get($response->headers(), 'X-RateLimit-Reset');
    }
}
