<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

/**
 * Class LetterEventCollectionParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class LetterEventCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return LetterEventCollectionParameterBag
     */
    public function setFieldsLetter(array $fields): self
    {
        $this->set('fields[letters]', collect($fields)->join(','));

        return $this;
    }

    /**
     * @param array $fields
     * @return LetterEventCollectionParameterBag
     */
    public function setFieldsLetterEvent(array $fields): self
    {
        $this->set('fields[letters_events]', collect($fields)->join(','));

        return $this;
    }
}
