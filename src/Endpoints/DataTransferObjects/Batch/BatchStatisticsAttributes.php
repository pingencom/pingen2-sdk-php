<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchStatisticsAttributes extends DataTransferObject
{
    public int $letter_validating;

    public array $letter_groups;

    public array $letter_countries;
}
