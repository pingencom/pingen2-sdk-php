<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Webhook;

use Pingen\Support\DataTransferObject\DataTransferObject;

class WebhookDetails extends DataTransferObject
{
    public WebhookDetailsData $data;
}
