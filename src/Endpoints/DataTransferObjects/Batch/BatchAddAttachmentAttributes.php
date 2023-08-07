<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Support\Input;

/**
 * @method BatchAddAttachmentAttributes setFileUrl(string $value)
 * @method BatchAddAttachmentAttributes setFileUrlSignature(string $value)
 */
class BatchAddAttachmentAttributes extends Input
{
    protected string $file_url;

    protected string $file_url_signature;
}
