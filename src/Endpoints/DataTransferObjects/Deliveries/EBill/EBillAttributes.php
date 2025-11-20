<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\EBill;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\EBill
 */
class EBillAttributes extends DataTransferObject
{
    public string $status;

    public string $file_original_name;

    public ?int $file_pages;

    public ?string $recipient_identifier;

    public ?string $invoice_number;

    public ?string $invoice_date;

    public ?string $invoice_due_date;

    /**
     * @var float|int|null
     */
    public $invoice_value;

    public ?string $invoice_currency;

    /**
     * @var float|int|null
     */
    public $price_value;

    public ?string $price_currency;

    public ?string $source;

    public ?CarbonImmutable $submitted_at;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
