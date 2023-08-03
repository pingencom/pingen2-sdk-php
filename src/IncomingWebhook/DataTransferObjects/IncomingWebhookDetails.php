<?php

declare(strict_types=1);

namespace Pingen\IncomingWebhook\DataTransferObjects;

use Pingen\Support\DataTransferObject\DataTransferObject;

class IncomingWebhookDetails extends DataTransferObject
{
    public IncomingWebhookDetailsData $data;
}
