<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\DataTransferObject;

/**
 * Class LetterDetails
 * @package Pingen\DataTransferObjects\Letter
 */
class LetterDetails extends DataTransferObject
{
    public LetterDetailsData $data;
}
