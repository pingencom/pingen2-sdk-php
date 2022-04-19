<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Webhook;

use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Support\DataTransferObject;

/**
 * Class WebhookCollection
 * @package Pingen\DataTransferObjects\Letter
 */
class WebhookCollection extends DataTransferObject
{
    /**
     * @var \Pingen\Endpoints\DataTransferObjects\Webhook\WebhookCollectionItem[]
     */
    public array $data;

    public CollectionLinks $links;

    public CollectionMeta $meta;
}
