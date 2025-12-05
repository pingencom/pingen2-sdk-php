<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

/**
 * @package Pingen\Endpoints\ParameterBags
 */
class EbillCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return EbillCollectionParameterBag
     */
    public function setFieldsEBill(array $fields): self
    {
        $this->set('fields[ebills]', collect($fields)->join(','));

        return $this;
    }
}
