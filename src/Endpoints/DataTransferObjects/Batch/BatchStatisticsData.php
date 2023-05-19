<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchStatisticsData extends DataTransferObject
{
    public ?string $id;

    public string $type;

    public BatchStatisticsAttributes $attributes;

    public ?ItemLinks $links;
}
