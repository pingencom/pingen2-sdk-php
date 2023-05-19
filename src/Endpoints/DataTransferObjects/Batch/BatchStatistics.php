<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchStatistics extends DataTransferObject
{
    public BatchStatisticsData $data;
}
