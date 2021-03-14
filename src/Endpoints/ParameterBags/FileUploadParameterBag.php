<?php

declare(strict_types=1);

namespace Pingen\Endpoints\ParameterBags;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class FileUploadParameterBag
 * @package Pingen\Endpoints\ParameterBags
 */
class FileUploadParameterBag extends ParameterBag
{
    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields): self
    {
        $this->set('fields[file_uploads]', collect($fields)->join(','));

        return $this;
    }
}
