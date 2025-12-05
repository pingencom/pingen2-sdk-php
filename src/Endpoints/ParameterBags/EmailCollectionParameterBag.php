<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

/**
 * @package Pingen\Endpoints\ParameterBags
 */
class EmailCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return EmailCollectionParameterBag
     */
    public function setFieldsEmail(array $fields): self
    {
        $this->set('fields[emails]', collect($fields)->join(','));

        return $this;
    }
}
