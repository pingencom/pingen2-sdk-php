<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\EbillEvent;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class EbillEventAttributes
 * @package Pingen\Endpoints\DataTransferObjects\EbillEvent
 */
class EbillEventAttributes extends DataTransferObject
{
    public string $code;

    public string $name;

    public string $producer;

    public ?string $location;

    public bool $has_image;

    public ?array $data;

    public CarbonImmutable $emitted_at;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
