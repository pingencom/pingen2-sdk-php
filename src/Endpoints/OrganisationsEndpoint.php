<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationCollection;
use Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationCollectionItem;
use Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationDetails;
use Pingen\Endpoints\ParameterBags\OrganisationCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\OrganisationParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;

/**
 * Class OrganisationsEndpoint
 * @package Pingen\Endpoints
 */
class OrganisationsEndpoint extends ResourceEndpoint
{
    /**
     * @param string $organisationId
     * @param OrganisationParameterBag|null $parameterBag
     * @return OrganisationDetails
     * @throws RequestException
     */
    public function getDetails(string $organisationId, ?OrganisationParameterBag $parameterBag = null): OrganisationDetails
    {
        return new OrganisationDetails(
            $this->performGetDetailsRequest(
                sprintf('/organisations/%s', $organisationId),
                $parameterBag ?? (new OrganisationParameterBag())
            )->json()
        );
    }

    /**
     * @param OrganisationCollectionParameterBag|null $organisationCollectionParameterBag
     * @return OrganisationCollection
     * @throws RateLimitJsonApiException
     * @throws RequestException
     */
    public function getCollection(?OrganisationCollectionParameterBag $organisationCollectionParameterBag = null): OrganisationCollection
    {
        return new OrganisationCollection(
            $this
                ->performGetCollectionRequest(
                    '/organisations',
                    $organisationCollectionParameterBag ?? (new OrganisationCollectionParameterBag())
                )->json()
        );
    }

    /**
     * @param OrganisationCollectionParameterBag|null $listParameterBag
     * @return \Generator|OrganisationCollectionItem[]
     * @throws RequestException
     */
    public function iterateOverCollection(?OrganisationCollectionParameterBag $listParameterBag = null)
    {
        if ($listParameterBag === null) {
            $listParameterBag = new OrganisationCollectionParameterBag();
        }

        try {
            do {
                $collection = $this->getCollection($listParameterBag);

                foreach ($collection->data as $collectionItem) {
                    yield $collectionItem; // @codeCoverageIgnore
                }

                $listParameterBag->setPageNumber($collection->meta->current_page + 1);
            } while ($collection->links->next);
        } catch (RateLimitJsonApiException $rateLimitJsonApiException) {
            sleep($rateLimitJsonApiException->retryAfter);

            $this->iterateOverCollection($listParameterBag);
        }
    }
}
