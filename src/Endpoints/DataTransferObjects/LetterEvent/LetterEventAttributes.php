<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\LetterEvent;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class LetterEventAttributes
 * @package Pingen\Endpoints\DataTransferObjects\LetterEvent
 */
class LetterEventAttributes extends DataTransferObject
{
    public string $code;

    public string $name;

    public string $producer;

    public string $location;

    public bool $has_image;

    public array $data;

    public CarbonImmutable $emitted_at;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
