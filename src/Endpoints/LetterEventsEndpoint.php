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
 * Class LettersEndpoint
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
}
