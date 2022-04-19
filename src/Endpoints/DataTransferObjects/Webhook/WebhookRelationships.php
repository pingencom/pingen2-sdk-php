<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Webhook;

use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Support\DataTransferObject;

/**
 * Class WebhookRelationships
 * @package Pingen\Endpoints\DataTransferObjects\Letter
 */
class WebhookRelationships extends DataTransferObject
{
    public RelationshipRelatedItem $organisation;
}
