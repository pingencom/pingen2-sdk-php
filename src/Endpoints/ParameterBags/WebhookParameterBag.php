<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Symfony\Component\HttpFoundation\ParameterBag;

class WebhookParameterBag extends ParameterBag
{
    public function setFields(array $fields): self
    {
        $this->set('fields[webhooks]', collect($fields)->join(','));

        return $this;
    }
}