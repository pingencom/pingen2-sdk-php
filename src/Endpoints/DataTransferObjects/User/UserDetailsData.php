<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\User;

use Pingen\Endpoints\DataTransferObjects\General\ItemLinks;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class UserDetailsData
 * @package Pingen\Endpoints\DataTransferObjects\User
 */
class UserDetailsData extends DataTransferObject
{
    public ?string $id;

    public string $type;

    public UserAttributes $attributes;

    public ?ItemLinks $links;
}
