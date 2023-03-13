<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class DetailsItemMetaAbilitiesSelf
 * @package Pingen\DataTransferObjects\General
 */
class LetterDetailsMetaAbilitiesSelf extends DataTransferObject
{
    public string $cancel;

    public string $delete;

    public string $submit;
}
