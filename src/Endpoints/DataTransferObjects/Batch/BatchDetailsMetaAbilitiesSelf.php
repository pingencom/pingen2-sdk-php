<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchDetailsMetaAbilitiesSelf extends DataTransferObject
{
    public string $cancel;

    public string $delete;

    public string $submit;
}
