<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\FileUpload;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class FileUploadDetailsData
 * @package Pingen\DataTransferObjects\FileUpload
 */
class FileUploadDetailsData extends DataTransferObject
{
    public string $type;

    public string $id;

    public FileUploadAttributes $attributes;
}
