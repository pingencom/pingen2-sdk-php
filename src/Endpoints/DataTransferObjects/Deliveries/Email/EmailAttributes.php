<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Deliveries\Email;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * @package Pingen\DataTransferObjects\Deliveries\Email
 */
class EmailAttributes extends DataTransferObject
{
    public string $status;

    public string $file_original_name;

    public ?int $file_pages;

    public ?string $recipient_identifier;

    /**
     * @var float|int|null
     */
    public $price_value;

    public ?string $price_currency;

    public ?string $source;

    public ?CarbonImmutable $submitted_at;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
