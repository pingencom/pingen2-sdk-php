<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\BatchEvent;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchEventAttributes extends DataTransferObject
{
    public string $code;

    public string $name;

    public string $producer;

    public string $location;

    public array $data;

    public CarbonImmutable $emitted_at;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
