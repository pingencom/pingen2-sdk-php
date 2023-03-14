<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\FileUpload;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class FileUploadDetails
 * @package Pingen\DataTransferObjects\FileUpload
 */
class FileUploadDetails extends DataTransferObject
{
    public FileUploadDetailsData $data;
}
