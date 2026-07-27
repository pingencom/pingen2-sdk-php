<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

/**
 * Class EbillEventCollectionParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class EbillEventCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return EbillEventCollectionParameterBag
     */
    public function setFieldsEbill(array $fields): self
    {
        $this->set('fields[ebills]', collect($fields)->join(','));

        return $this;
    }

    /**
     * @param array $fields
     * @return EbillEventCollectionParameterBag
     */
    public function setFieldsEbillEvent(array $fields): self
    {
        $this->set('fields[deliverables_events]', collect($fields)->join(','));

        return $this;
    }

    /**
     * @param string $language
     * @return EbillEventCollectionParameterBag
     */
    public function setLanguage(string $language): self
    {
        $this->set('language', $language);

        return $this;
    }
}
