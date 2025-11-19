<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

/**
 * @package Pingen\Endpoints\ParameterBags
 */
class EBillCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return EBillCollectionParameterBag
     */
    public function setFieldsEBill(array $fields): self
    {
        $this->set('fields[ebills]', collect($fields)->join(','));

        return $this;
    }
}
