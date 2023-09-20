<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\Input;

/**
 * Class LetterAddAttachmentAttributes
 * @package Pingen\DataTransferObjects\Letter
 *
 * @method LetterAddAttachmentAttributes setFileUrl(string $value)
 * @method LetterAddAttachmentAttributes setFileUrlSignature(string $value)
 */
class LetterAddAttachmentAttributes extends Input
{
    protected string $file_url;

    protected string $file_url_signature;
}
