<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\UserAssociation;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject;

/**
 * Class UserAssociationItemAttributes
 * @package Pingen\Models
 */
class UserAssociationAttributes extends DataTransferObject
{
    public string $role;

    public string $status;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
