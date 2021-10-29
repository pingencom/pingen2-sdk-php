<?php

declare(strict_types=1);

namespace Pingen\Support;

/**
 * Trait HasLetterContext
 * @package Pingen\Support
 */
trait HasLetterContext
{
    protected string $letterId;

    /**
     * @param string $letterId
     * @return static
     */
    public function setLetterId(string $letterId)
    {
        $this->letterId = $letterId;

        return $this;
    }

    /**
     * @return string
     */
    public function getLetterId(): string
    {
        if (! $this->letterId) {
            throw new \RuntimeException('Letter id has to be set first.'); //@codeCoverageIgnore
        }

        return $this->letterId;
    }
}
