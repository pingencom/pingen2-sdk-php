<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedMany;
use Pingen\Support\DataTransferObject;

/**
 * Class LetterRelationships
 * @package Pingen\Endpoints\DataTransferObjects\Letter
 */
class LetterRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $organisation;

    public RelationshipRelatedMany $events;
}
