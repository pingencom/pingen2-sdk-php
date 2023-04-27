<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\BatchEvent\BatchEventCollection;
use Pingen\Endpoints\ParameterBags\BatchEventCollectionParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasBatchContext;
use Pingen\Support\HasOrganisationContext;

class BatchEventsEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext, HasBatchContext;

    /**
     * @param BatchEventCollectionParameterBag|null $batchEventCollectionParameterBag
     * @return BatchEventCollection
     * @throws RateLimitJsonApiException
     * @throws RequestException
     */
    public function getCollection(?BatchEventCollectionParameterBag $batchEventCollectionParameterBag = null): BatchEventCollection
    {
        return new BatchEventCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/batches/%s/events', $this->getOrganisationId(), $this->getBatchId()),
                    $batchEventCollectionParameterBag ?? (new BatchEventCollectionParameterBag())
                )->json()
        );
    }
}
