<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Pingen\Endpoints\DataTransferObjects\LetterEvent\LetterEventCollection;
use Pingen\Endpoints\ParameterBags\LetterEventCollectionParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasLetterContext;
use Pingen\Support\HasOrganisationContext;

/**
 * Class LetterEventsEndpoint
 * @package Pingen\Endpoints
 */
class LetterEventsEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;
    use HasLetterContext;

    /**
     * @param LetterEventCollectionParameterBag|null $letterEventCollectionParameterBag
     * @return LetterEventCollection
     * @throws RateLimitJsonApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getCollection(?LetterEventCollectionParameterBag $letterEventCollectionParameterBag = null): LetterEventCollection
    {
        return new LetterEventCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/letters/%s/events', $this->getOrganisationId(), $this->getLetterId()),
                    $letterEventCollectionParameterBag ?? (new LetterEventCollectionParameterBag())
                )->json()
        );
    }

    /**
     * @param LetterEventCollectionParameterBag|null $letterEventCollectionParameterBag
     * @return LetterEventCollection
     * @throws RateLimitJsonApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getIssuesCollection(?LetterEventCollectionParameterBag $letterEventCollectionParameterBag = null): LetterEventCollection
    {
        return new LetterEventCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/letters/events/issues', $this->getOrganisationId()),
                    $letterEventCollectionParameterBag ?? (new LetterEventCollectionParameterBag())
                )->json()
        );
    }

    /**
     * @param LetterEventCollectionParameterBag|null $letterEventCollectionParameterBag
     * @return LetterEventCollection
     * @throws RateLimitJsonApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getSentCollection(?LetterEventCollectionParameterBag $letterEventCollectionParameterBag = null): LetterEventCollection
    {
        return new LetterEventCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/letters/events/sent', $this->getOrganisationId()),
                    $letterEventCollectionParameterBag ?? (new LetterEventCollectionParameterBag())
                )->json()
        );
    }

    /**
     * @param LetterEventCollectionParameterBag|null $letterEventCollectionParameterBag
     * @return LetterEventCollection
     * @throws RateLimitJsonApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getUndeliverableCollection(?LetterEventCollectionParameterBag $letterEventCollectionParameterBag = null): LetterEventCollection
    {
        return new LetterEventCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/letters/events/undeliverable', $this->getOrganisationId()),
                    $letterEventCollectionParameterBag ?? (new LetterEventCollectionParameterBag())
                )->json()
        );
    }
}
