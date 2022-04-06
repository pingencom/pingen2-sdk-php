<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

/**
 * Class UserAssociationCollectionParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class UserAssociationCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return UserAssociationCollectionParameterBag
     */
    public function setFieldsAssociation(array $fields): self
    {
        $this->set('fields[associations]', collect($fields)->join(','));

        return $this;
    }

    /**
     * @param array $fields
     * @return UserAssociationCollectionParameterBag
     */
    public function setFieldsOrganisations(array $fields): self
    {
        $this->set('fields[organisations]', collect($fields)->join(','));

        return $this;
    }
}
