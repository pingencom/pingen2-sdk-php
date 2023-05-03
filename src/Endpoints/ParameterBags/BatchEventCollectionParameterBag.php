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
    public function setFieldsBatch(array $fields): self
    {
        $this->set('fields[batches]', collect($fields)->join(','));

        return $this;
    }

    /**
     * @param array $fields
     * @return BatchEventCollectionParameterBag
     */
    public function setFieldsBatchEvent(array $fields): self
    {
        $this->set('fields[batches_events]', collect($fields)->join(','));

        return $this;
    }

    /**
     *
     * @param string $language
     * @return BatchEventCollectionParameterBag
     */
    public function setLanguage(string $language): self
    {
        $this->set('language', $language);

        return $this;
    }
}
