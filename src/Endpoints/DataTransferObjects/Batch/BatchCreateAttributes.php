<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Support\Input;

/**
 * @method BatchCreateAttributes setName(string $value)
 * @method BatchCreateAttributes setIcon(string $value)
 * @method BatchCreateAttributes setFileOriginalName(string $value)
 * @method BatchCreateAttributes setFileUrl(string $value)
 * @method BatchCreateAttributes setFileUrlSignature(string $value)
 * @method BatchCreateAttributes setAddressPosition(string $value)
 * @method BatchCreateAttributes setGroupingType(string $value)
 * @method BatchCreateAttributes setGroupingOptionsSplitType(string $value)
 * @method BatchCreateAttributes setGroupingOptionsSplitSize(string $value)
 * @method BatchCreateAttributes setGroupingOptionsSplitSeparator(string $value)
 */
class BatchCreateAttributes extends Input
{
    protected string $name;

    protected string $icon;

    protected string $file_original_name;

    protected string $file_url;

    protected string $file_url_signature;

    protected string $address_position;

    protected string $grouping_type;

    protected ?string $grouping_options_split_type;

    protected ?string $grouping_options_split_size;

    protected ?string $grouping_options_split_separator;
}
