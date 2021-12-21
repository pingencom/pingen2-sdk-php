<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\User;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject;

/**
 * Class UserAttributes
 * @package Pingen\Endpoints\DataTransferObjects\User
 */
class UserAttributes extends DataTransferObject
{
    public string $email;

    public string $first_name;

    public string $last_name;

    public string $status;

    public string $language;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
