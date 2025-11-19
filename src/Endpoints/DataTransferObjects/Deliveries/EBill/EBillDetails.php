<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\EBill;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\EBill
 */
class EBillDetails extends DataTransferObject
{
    public EBillDetailsData $data;
}
