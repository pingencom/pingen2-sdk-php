<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\UserAssociation\UserAssociationDetails;
use Pingen\Endpoints\DataTransferObjects\UserAssociation\UserAssociationsCollection;
use Pingen\Endpoints\DataTransferObjects\UserAssociation\UserAssociationsCollectionItem;
use Pingen\Endpoints\ParameterBags\UserAssociationCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\UserAssociationParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;

/**
 * Class UserAssociationsEndpoint
 * @package Pingen\Endpoints
 */
class UserAssociationsEndpoint extends ResourceEndpoint
{
    /**
     * @param UserAssociationCollectionParameterBag|null $listParameterBag
     * @return UserAssociationsCollection
     * @throws RequestException
     */
    public function getCollection(?UserAssociationCollectionParameterBag $listParameterBag = null): UserAssociationsCollection
    {
        return new UserAssociationsCollection(
            $this
                ->performGetCollectionRequest(
                    '/user/associations',
                    $listParameterBag ?? (new UserAssociationCollectionParameterBag())
                )
                ->json()
        );
    }

    /**
     * @param UserAssociationCollectionParameterBag|null $listParameterBag
     * @return \Generator|UserAssociationsCollectionItem[]
     * @throws RequestException
     */
    public function iterateOverCollection(?UserAssociationCollectionParameterBag $listParameterBag = null)
    {
        if ($listParameterBag === null) {
            $listParameterBag = new UserAssociationCollectionParameterBag();
        }

        try {
            do {
                $collection = $this->getCollection($listParameterBag);

                foreach ($collection->data as $collectionItem) {
                    yield $collectionItem;
                }

                $listParameterBag->setPageNumber($collection->meta->current_page + 1);
            } while ($collection->links->next);
        } catch (RateLimitJsonApiException $rateLimitJsonApiException) {
            sleep($rateLimitJsonApiException->retryAfter);

            $this->iterateOverCollection($listParameterBag);
        }
    }

    /**
     * @param string $associationId
     * @param UserAssociationParameterBag|null $parameterBag
     * @return UserAssociationDetails
     * @throws RequestException
     */
    public function getDetails(string $associationId, ?UserAssociationParameterBag $parameterBag = null): UserAssociationDetails
    {
        return new UserAssociationDetails(
            $this->performGetDetailsRequest(
                '/user/associations/' . $associationId,
                $parameterBag ?? (new UserAssociationParameterBag())
            )->json()
        );
    }
}
