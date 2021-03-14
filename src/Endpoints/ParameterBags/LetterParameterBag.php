<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class LetterParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class LetterParameterBag extends ParameterBag
{
    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields): self
    {
        $this->set('fields[letters]', collect($fields)->join(','));

        return $this;
    }
}
