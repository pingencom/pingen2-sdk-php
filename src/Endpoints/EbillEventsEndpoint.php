<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\EbillEvent\EbillEventCollection;
use Pingen\Endpoints\ParameterBags\EbillEventCollectionParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasEbillContext;
use Pingen\Support\HasOrganisationContext;

/**
 * Class EbillEventsEndpoint
 * @package Pingen\Endpoints
 */
class EbillEventsEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;
    use HasEbillContext;

    /**
     * @param EbillEventCollectionParameterBag|null $ebillEventCollectionParameterBag
     * @return EbillEventCollection
     * @throws RateLimitJsonApiException
     * @throws RequestException
     */
    public function getCollection(?EbillEventCollectionParameterBag $ebillEventCollectionParameterBag = null): EbillEventCollection
    {
        return new EbillEventCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/deliveries/ebills/%s/events', $this->getOrganisationId(), $this->getEbillId()),
                    $ebillEventCollectionParameterBag ?? (new EbillEventCollectionParameterBag())
                )->json()
        );
    }
}
