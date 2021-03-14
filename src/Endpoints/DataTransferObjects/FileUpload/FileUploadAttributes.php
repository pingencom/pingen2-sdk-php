<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\FileUpload;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject;

/**
 * Class FileUploadAttributes
 * @package Pingen\DataTransferObjects\FileUpload
 */
class FileUploadAttributes extends DataTransferObject
{
    public string $url;

    public string $url_signature;

    public CarbonImmutable $expires_at;
}
