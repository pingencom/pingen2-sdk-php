<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Batch;

use Pingen\Support\Input;

/**
 * @method BatchEditAttributes setName(string $values)
 * @method BatchEditAttributes setIcon(string $values)
 */
class BatchEditAttributes extends Input
{
    protected string $name;

    protected string $icon;
}
