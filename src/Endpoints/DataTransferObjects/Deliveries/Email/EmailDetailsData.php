<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Email;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Email
 */
class EmailDetailsData extends DataTransferObject
{
    public ?string $id;

    public string $type;

    public EmailAttributes $attributes;

    public ?ItemLinks $links;

    public ?EmailRelationships $relationships;
}
