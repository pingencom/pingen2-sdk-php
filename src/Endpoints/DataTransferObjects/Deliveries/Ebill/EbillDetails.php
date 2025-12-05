<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Ebill
 */
class EbillDetails extends DataTransferObject
{
    public EbillDetailsData $data;
}
