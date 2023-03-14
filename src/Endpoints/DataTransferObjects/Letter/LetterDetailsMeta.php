<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class LetterDetailsMeta
 * @package Pingen\DataTransferObjects\General
 */
class LetterDetailsMeta extends DataTransferObject
{
    public LetterDetailsMetaAbilities $abilities;
}
