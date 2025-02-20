<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class LetterAttributes
 * @package Pingen\DataTransferObjects\Letter
 */
class LetterAttributes extends DataTransferObject
{
    public string $status;

    public string $file_original_name;

    public ?int $file_pages;

    public string $address_position;

    public ?string $address;

    public ?string $country;

    public ?string $price_currency;

    /**
     * @var float|int|null
     */
    public $price_value;

    public ?string $delivery_product;

    public ?string $print_mode;

    public ?string $print_spectrum;

    public ?array $paper_types;

    public ?array $fonts;

    public ?string $source;

    public ?string $tracking_number;

    public ?CarbonImmutable $submitted_at;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
