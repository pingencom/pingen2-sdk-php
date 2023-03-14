<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\LetterEvent;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class LetterEventRelationships
 * @package Pingen\Endpoints\DataTransferObjects\LetterEvent
 */
class LetterEventRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $letter;
}
