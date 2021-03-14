<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Carbon\CarbonImmutable;
use Pingen\Support\DataTransferObject;

/**
 * Class LetterAttributes
 * @package Pingen\DataTransferObjects\Letter
 */
class LetterAttributes extends DataTransferObject
{
    public string $status;

    public string $file_original_name;

    public string $address_position;

    public ?string $delivery_product;

    public ?string $print_mode;

    public ?string $print_spectrum;

    public CarbonImmutable $created_at;

    public CarbonImmutable $updated_at;
}
