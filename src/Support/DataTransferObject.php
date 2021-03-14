<?php

declare(strict_types=1);

namespace Pingen\Support;

use Spatie\DataTransferObject\FieldValidator;
use Spatie\DataTransferObject\FlexibleDataTransferObject;
use Spatie\DataTransferObject\ValueCaster;

/**
 * Stolen from https://github.com/spatie/data-transfer-object/issues/121
 * Class DataTransferObject
 * @package Pingen\Support
 */
abstract class DataTransferObject extends FlexibleDataTransferObject
{
    /**
     * - The key is the type of the property
     * - The callable receives the value and may return the same or a different value
     * @var array<string,callable>
     */
    public static array $makers = [];

    /**
     * @inheritDoc
     */
    protected function castValue(ValueCaster $valueCaster, FieldValidator $fieldValidator, $value)
    {
        if (is_array($value)) {
            return parent::castValue($valueCaster, $fieldValidator, $value);
        }

        return $this->makeValue($fieldValidator, $value);
    }

    /**
     * @param FieldValidator $fieldValidator
     * @param mixed $value
     * @return mixed
     */
    protected function makeValue(FieldValidator $fieldValidator, $value)
    {
        foreach ($fieldValidator->allowedTypes as $type) {
            $type = ltrim($type, '\\');

            if (! isset(self::$makers[$type])) {
                continue;
            }

            return self::$makers[$type]($value);
        }

        return $value;
    }
}
