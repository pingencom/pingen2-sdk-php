<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject\DataTransferObject;

class BatchAttributes extends DataTransferObject
{
    public string $name;

    public string $icon;

    public ?string $channel_type;

    public string $status;

    public string $file_original_name;

    /**
     * @deprecated The api replaced this with deliverable_count, use that one instead.
     */
    public ?int $letter_count;

    public ?int $deliverable_count;

    public string $address_position;

    public ?string $price_currency;

    /**
     * @var float|int|null
     */
    public $price_value;

    public ?string $print_mode;

    public ?string $print_spectrum;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
