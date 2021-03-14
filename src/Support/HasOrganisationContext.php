<?php

declare(strict_types=1);

namespace Pingen\Support;

/**
 * Trait HasCompanyContext
 * @package Pingen\Support
 */
trait HasOrganisationContext
{
    protected string $organisationId;

    /**
     * @param string $organisationId
     * @return static
     */
    public function setOrganisationId(string $organisationId)
    {
        $this->organisationId = $organisationId;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrganisationId(): string
    {
        if (! $this->organisationId) {
            throw new \RuntimeException('Organisation id has to be set first.'); //@codeCoverageIgnore
        }

        return $this->organisationId;
    }
}
