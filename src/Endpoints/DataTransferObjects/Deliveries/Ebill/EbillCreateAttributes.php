<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill;

use Pingen\Support\Input;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Ebill
 *
 * @method EbillCreateAttributes setFileUrl(string $value)
 * @method EbillCreateAttributes setFileUrlSignature(string $value)
 * @method EbillCreateAttributes setFileOriginalName(string $value)
 * @method EbillCreateAttributes setAutoSend(bool $value)
 * @method EbillCreateAttributes setMetaData(EbillMetaDataAttributes $metaData)
 */
class EbillCreateAttributes extends Input
{
    protected string $file_original_name;

    protected string $file_url;

    protected string $file_url_signature;

    protected bool $auto_send;

    protected ?EbillMetaDataAttributes $meta_data;
}
