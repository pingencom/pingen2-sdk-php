<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\EmailEvent\EmailEventCollection;
use Pingen\Endpoints\ParameterBags\EmailEventCollectionParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasEmailContext;
use Pingen\Support\HasOrganisationContext;

/**
 * Class EmailEventsEndpoint
 * @package Pingen\Endpoints
 */
class EmailEventsEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;
    use HasEmailContext;

    /**
     * @param EmailEventCollectionParameterBag|null $emailEventCollectionParameterBag
     * @return EmailEventCollection
     * @throws RateLimitJsonApiException
     * @throws RequestException
     */
    public function getCollection(?EmailEventCollectionParameterBag $emailEventCollectionParameterBag = null): EmailEventCollection
    {
        return new EmailEventCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/deliveries/emails/%s/events', $this->getOrganisationId(), $this->getEmailId()),
                    $emailEventCollectionParameterBag ?? (new EmailEventCollectionParameterBag())
                )->json()
        );
    }
}
