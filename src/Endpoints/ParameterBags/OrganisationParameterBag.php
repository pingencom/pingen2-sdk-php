<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class CompanyParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class OrganisationParameterBag extends ParameterBag
{
    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields): self
    {
        $this->set('fields[companies]', collect($fields)->join(','));

        return $this;
    }
}
