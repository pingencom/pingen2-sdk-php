<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

/**
 * Class OrganisationCollectionParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class OrganisationCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return OrganisationCollectionParameterBag
     */
    public function setFieldsOrganisation(array $fields): self
    {
        $this->set('fields[organisations]', collect($fields)->join(','));

        return $this;
    }
}
