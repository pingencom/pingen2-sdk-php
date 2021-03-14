<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\General;

use Pingen\Support\DataTransferObject;

/**
 * Class CollectionLinks
 * @package Pingen\DataTransferObjects\General
 */
class CollectionLinks extends DataTransferObject
{
    public string $first;

    public string $last;

    public ?string $prev;

    public ?string $next;

    public string $self;
}
