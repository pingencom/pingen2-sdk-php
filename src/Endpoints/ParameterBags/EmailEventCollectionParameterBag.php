<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Pingen\Support\CollectionParameterBag;

/**
 * Class EmailEventCollectionParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class EmailEventCollectionParameterBag extends CollectionParameterBag
{
    /**
     * @param array $fields
     * @return EmailEventCollectionParameterBag
     */
    public function setFieldsEmail(array $fields): self
    {
        $this->set('fields[emails]', collect($fields)->join(','));

        return $this;
    }

    /**
     * @param array $fields
     * @return EmailEventCollectionParameterBag
     */
    public function setFieldsEmailEvent(array $fields): self
    {
        $this->set('fields[deliverables_events]', collect($fields)->join(','));

        return $this;
    }

    /**
     * @param string $language
     * @return EmailEventCollectionParameterBag
     */
    public function setLanguage(string $language): self
    {
        $this->set('language', $language);

        return $this;
    }
}
