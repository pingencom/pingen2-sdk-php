<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Organisation;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject;

class OrganisationAttributes extends DataTransferObject
{
    public ?string $name;

    public ?string $status;

    public ?string $plan;

    public ?string $billing_currency;

    public ?string $default_country;

    public ?string $default_address_position;

    public ?int $data_retention_addresses;

    public ?int $data_retention_pdf;

    public ?string $color;

    public ?CarbonImmutable $created_at;

    public ?CarbonImmutable $updated_at;
}
