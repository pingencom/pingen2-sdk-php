<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\Input;

/**
 * Class AddAttachmentToMultipleLettersAttributes
 * @package Pingen\DataTransferObjects\Letter
 *
 * @method AddAttachmentToMultipleLettersAttributes setLetterIds(array $value)
 * @method AddAttachmentToMultipleLettersAttributes setFileUrl(string $value)
 * @method AddAttachmentToMultipleLettersAttributes setFileUrlSignature(string $value)
 */
class AddAttachmentToMultipleLettersAttributes extends Input
{
    /** @var string[] */
    protected array $letter_ids;

    protected string $file_url;

    protected string $file_url_signature;
}
