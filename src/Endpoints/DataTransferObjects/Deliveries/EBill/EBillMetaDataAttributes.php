<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\EBill;

use Pingen\Support\Input;

/**
 * @package Pingen\DataTransferObjects\Deliveries\EBill
 *
 * @method EBillMetaDataAttributes setInvoiceNumber(string $value)
 * @method EBillMetaDataAttributes setInvoiceDate(string $value)
 * @method EBillMetaDataAttributes setInvoiceDueDate(string $value)
 * @method EBillMetaDataAttributes setRecipientIdentifier(string $value)
 */
class EBillMetaDataAttributes extends Input
{
    protected ?string $invoice_number;

    protected ?string $invoice_date;

    protected ?string $invoice_due_date;

    protected ?string $recipient_identifier;
}
