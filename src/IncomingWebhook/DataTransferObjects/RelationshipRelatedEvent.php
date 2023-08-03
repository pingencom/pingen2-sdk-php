<?php

declare(strict_types=1);

namespace Pingen\IncomingWebhook\DataTransferObjects;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItemData;
use Pingen\Support\DataTransferObject\DataTransferObject;

class RelationshipRelatedEvent extends DataTransferObject
{
    public RelationshipRelatedItemData $data;
}
