<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\User;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class UserDetails
 * @package Pingen\Endpoints\DataTransferObjects\User
 */
class UserDetails extends DataTransferObject
{
    public UserDetailsData $data;
}
