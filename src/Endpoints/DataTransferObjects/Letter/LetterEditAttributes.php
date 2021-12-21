<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\Input;

/**
 * Class LetterEditAttributes
 * @package Pingen\DataTransferObjects\Letter
 *
 * @method LetterEditAttributes setPaperTypes(array $values)
 */
class LetterEditAttributes extends Input
{
    protected array $paper_types;
}
