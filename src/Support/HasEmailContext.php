<?php

declare(strict_types=1);

namespace Pingen\Support;

/**
 * Trait HasEmailContext
 * @package Pingen\Support
 */
trait HasEmailContext
{
    protected string $emailId;

    /**
     * @param string $emailId
     * @return static
     */
    public function setEmailId(string $emailId)
    {
        $this->emailId = $emailId;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailId(): string
    {
        if (! $this->emailId) {
            throw new \RuntimeException('Email id has to be set first.'); //@codeCoverageIgnore
        }

        return $this->emailId;
    }
}
