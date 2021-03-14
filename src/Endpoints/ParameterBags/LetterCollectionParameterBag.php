<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

/**
 * Class LetterCollectionParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class LetterCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return LetterCollectionParameterBag
     */
    public function setFieldsLetter(array $fields): self
    {
        $this->set('fields[letters]', collect($fields)->join(','));

        return $this;
    }
}
