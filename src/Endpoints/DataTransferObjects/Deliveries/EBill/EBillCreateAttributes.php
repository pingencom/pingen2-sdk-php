<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\EBill;

use Pingen\Support\Input;

/**
 * @package Pingen\DataTransferObjects\Deliveries\EBill
 *
 * @method EBillCreateAttributes setFileUrl(string $value)
 * @method EBillCreateAttributes setFileUrlSignature(string $value)
 * @method EBillCreateAttributes setFileOriginalName(string $value)
 * @method EBillCreateAttributes setAutoSend(bool $value)
 * @method EBillCreateAttributes setMetaData(EBillMetaDataAttributes $metaData)
 */
class EBillCreateAttributes extends Input
{
    protected string $file_original_name;

    protected string $file_url;

    protected string $file_url_signature;

    protected bool $auto_send;

    protected ?EBillMetaDataAttributes $meta_data;
}
