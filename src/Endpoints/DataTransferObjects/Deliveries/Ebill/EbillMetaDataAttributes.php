<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill;

use Pingen\Support\Input;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Ebill
 *
 * @method EbillMetaDataAttributes setInvoiceNumber(string $value)
 * @method EbillMetaDataAttributes setInvoiceDate(string $value)
 * @method EbillMetaDataAttributes setInvoiceDueDate(string $value)
 * @method EbillMetaDataAttributes setRecipientIdentifier(string $value)
 */
class EbillMetaDataAttributes extends Input
{
    protected ?string $invoice_number;

    protected ?string $invoice_date;

    protected ?string $invoice_due_date;

    protected ?string $recipient_identifier;
}
