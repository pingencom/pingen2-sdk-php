<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\General;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class CollectionMeta
 * @package Pingen\DataTransferObjects\General
 */
class CollectionMeta extends DataTransferObject
{
    public int $current_page;

    public int $last_page;

    public int $per_page;

    public ?int $from;

    public ?int $to;

    public int $total;
}
