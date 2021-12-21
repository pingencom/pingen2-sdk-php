<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Pingen\Endpoints\DataTransferObjects\LetterEvent\LetterEventCollection;
use Pingen\Endpoints\ParameterBags\LetterIssuesCollectionParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasOrganisationContext;

/**
 * Class LetterIssuesEndpoint
 * @package Pingen\Endpoints
 */
class LetterIssuesEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;

    /**
     * @param LetterIssuesCollectionParameterBag|null $letterIssuesCollectionParameterBag
     * @return LetterEventCollection
     * @throws RateLimitJsonApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getCollection(?LetterIssuesCollectionParameterBag $letterIssuesCollectionParameterBag = null): LetterEventCollection
    {
        return new LetterEventCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/letters/issues', $this->getOrganisationId()),
                    $letterIssuesCollectionParameterBag ?? (new LetterIssuesCollectionParameterBag())
                )->json()
        );
    }
}
