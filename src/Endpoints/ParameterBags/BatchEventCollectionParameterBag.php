<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

class BatchEventCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return BatchEventCollectionParameterBag
     */
    public function setFieldsLetter(array $fields): self
    {
        $this->set('fields[batches]', collect($fields)->join(','));

        return $this;
    }

    /**
     * @param array $fields
     * @return BatchEventCollectionParameterBag
     */
    public function setFieldsLetterEvent(array $fields): self
    {
        $this->set('fields[batches_events]', collect($fields)->join(','));

        return $this;
    }
}
