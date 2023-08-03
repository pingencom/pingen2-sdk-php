<?php

declare(strict_types=1);

namespace Pingen\IncomingWebhook\DataTransferObjects;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Support\DataTransferObject\DataTransferObject;

class IncomingWebhookRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $organisation;

    public RelationshipRelatedItem $letter;

    public RelationshipRelatedEvent $event;
}
