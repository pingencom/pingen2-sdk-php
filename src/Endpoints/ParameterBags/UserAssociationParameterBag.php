<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class UserAssociationParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class UserAssociationParameterBag extends ParameterBag
{
    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields): self
    {
        $this->set('fields[users]', collect($fields)->join(','));

        return $this;
    }
}
