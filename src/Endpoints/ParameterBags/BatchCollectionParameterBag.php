<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

class BatchCollectionParameterBag extends CollectionParameterBag
{
    public function setFieldsBatch(array $fields): self
    {
        $this->set('fields[batches]', collect($fields)->join(','));

        return $this;
    }
}
