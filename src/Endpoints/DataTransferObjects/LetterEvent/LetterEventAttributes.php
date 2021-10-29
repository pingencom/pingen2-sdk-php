<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\LetterEvent;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject;

/**
 * Class LetterEventAttributes
 * @package Pingen\Endpoints\DataTransferObjects\Letter
 */
class LetterEventAttributes extends DataTransferObject
{
    public string $code;

    public string $producer;

    public string $location;

    public array $data;

    public CarbonImmutable $emitted_at;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
