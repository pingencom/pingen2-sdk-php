<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @package Pingen\Endpoints\ParameterBags
 */
class EmailParameterBag extends ParameterBag
{
    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields): self
    {
        $this->set('fields[emails]', collect($fields)->join(','));

        return $this;
    }
}
