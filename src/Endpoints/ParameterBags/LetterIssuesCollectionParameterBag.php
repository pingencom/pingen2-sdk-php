<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

/**
 * Class LetterIssuesCollectionParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class LetterIssuesCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return LetterIssuesCollectionParameterBag
     */
    public function setFieldsLetter(array $fields): self
    {
        $this->set('fields[letters]', collect($fields)->join(','));

        return $this;
    }

    /**
     * @param array $fields
     * @return LetterIssuesCollectionParameterBag
     */
    public function setFieldsLetterEvent(array $fields): self
    {
        $this->set('fields[letters_events]', collect($fields)->join(','));

        return $this;
    }
}
