<?php

declare(strict_types=1);

namespace Pingen\Support;

/**
 * Trait HasEbillContext
 * @package Pingen\Support
 */
trait HasEbillContext
{
    protected string $ebillId;

    /**
     * @param string $ebillId
     * @return static
     */
    public function setEbillId(string $ebillId)
    {
        $this->ebillId = $ebillId;

        return $this;
    }

    /**
     * @return string
     */
    public function getEbillId(): string
    {
        if (! $this->ebillId) {
            throw new \RuntimeException('Ebill id has to be set first.'); //@codeCoverageIgnore
        }

        return $this->ebillId;
    }
}
