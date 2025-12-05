<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Email;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Email
 */
class EmailDetails extends DataTransferObject
{
    public EmailDetailsData $data;
}
