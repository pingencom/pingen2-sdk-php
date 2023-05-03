<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

class WebhookCollectionParameterBag extends CollectionParameterBag
{
    public function setFields(array $fields): self
    {
        $this->set('fields[webhooks]', collect($fields)->join(','));

        return $this;
    }
}
