<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Exceptions\ValidationException;
use Pingen\Support\Input;

/**
 * @method BatchCreateAttributes setName(string $value)
 * @method BatchCreateAttributes setIcon(string $value)
 * @method BatchCreateAttributes setChannelType(string $value)
 * @method BatchCreateAttributes setFileOriginalName(string $value)
 * @method BatchCreateAttributes setFileUrl(string $value)
 * @method BatchCreateAttributes setFileUrlSignature(string $value)
 * @method BatchCreateAttributes setAddressPosition(string $value)
 * @method BatchCreateAttributes setGroupingType(string $value)
 * @method BatchCreateAttributes setGroupingOptionsSplitType(string $value)
 * @method BatchCreateAttributes setGroupingOptionsSplitSize(int $value)
 * @method BatchCreateAttributes setGroupingOptionsSplitSeparator(string $value)
 */
class BatchCreateAttributes extends Input
{
    public const NAME_MIN_LENGTH = 5;

    public const NAME_MAX_LENGTH = 100;

    public const CHANNEL_TYPES = ['post', 'ebill', 'email'];

    public const ICONS = [
        'campaign',
        'megaphone',
        'wave-hand',
        'flash',
        'rocket',
        'bell',
        'percent-tag',
        'percent-badge',
        'present',
        'receipt',
        'document',
        'information',
        'calendar',
        'newspaper',
        'crown',
        'virus',
    ];

    protected string $name;

    protected string $icon;

    protected ?string $channel_type;

    protected string $file_original_name;

    protected string $file_url;

    protected string $file_url_signature;

    protected string $address_position;

    protected string $grouping_type;

    protected ?string $grouping_options_split_type;

    protected ?int $grouping_options_split_size;

    protected ?string $grouping_options_split_separator;

    /**
     * @param string[] $excludedParameters
     * @return void
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function validate(array $excludedParameters = []): void
    {
        parent::validate($excludedParameters);

        $errorMsg = [];

        if (
            isset($this->name)
            && (mb_strlen($this->name) < self::NAME_MIN_LENGTH || mb_strlen($this->name) > self::NAME_MAX_LENGTH)
        ) {
            $errorMsg[] = sprintf(
                'The name field must be between %d and %d characters.',
                self::NAME_MIN_LENGTH,
                self::NAME_MAX_LENGTH
            );
        }

        if (isset($this->icon) && ! in_array($this->icon, self::ICONS, true)) {
            $errorMsg[] = sprintf('The icon field must be one of: %s.', implode(', ', self::ICONS));
        }

        if (isset($this->channel_type) && ! in_array($this->channel_type, self::CHANNEL_TYPES, true)) {
            $errorMsg[] = sprintf(
                'The channel_type field must be one of: %s.',
                implode(', ', self::CHANNEL_TYPES)
            );
        }

        if (count($errorMsg) > 0) {
            throw new ValidationException((string) json_encode($errorMsg));
        }
    }
}
