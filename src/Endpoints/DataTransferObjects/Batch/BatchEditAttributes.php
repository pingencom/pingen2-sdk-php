<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Exceptions\ValidationException;
use Pingen\Support\Input;

/**
 * Both attributes are optional, only the ones set are sent to the api.
 *
 * @method BatchEditAttributes setName(string $value)
 * @method BatchEditAttributes setIcon(string $value)
 */
class BatchEditAttributes extends Input
{
    protected ?string $name;

    protected ?string $icon;

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
            && (
                mb_strlen($this->name) < BatchCreateAttributes::NAME_MIN_LENGTH
                || mb_strlen($this->name) > BatchCreateAttributes::NAME_MAX_LENGTH
            )
        ) {
            $errorMsg[] = sprintf(
                'The name field must be between %d and %d characters.',
                BatchCreateAttributes::NAME_MIN_LENGTH,
                BatchCreateAttributes::NAME_MAX_LENGTH
            );
        }

        if (isset($this->icon) && ! in_array($this->icon, BatchCreateAttributes::ICONS, true)) {
            $errorMsg[] = sprintf(
                'The icon field must be one of: %s.',
                implode(', ', BatchCreateAttributes::ICONS)
            );
        }

        if (count($errorMsg) > 0) {
            throw new ValidationException((string) json_encode($errorMsg));
        }
    }
}
