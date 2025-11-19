<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Email;

use Pingen\Support\Input;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Email
 *
 * @method EmailCreateAttributes setFileUrl(string $value)
 * @method EmailCreateAttributes setFileUrlSignature(string $value)
 * @method EmailCreateAttributes setFileOriginalName(string $value)
 * @method EmailCreateAttributes setAutoSend(bool $value)
 * @method EmailCreateAttributes setMetaData(EmailMetaDataAttributes $metaData)
 */
class EmailCreateAttributes extends Input
{
    protected string $file_original_name;

    protected string $file_url;

    protected string $file_url_signature;

    protected bool $auto_send;

    protected ?EmailMetaDataAttributes $meta_data;
}
