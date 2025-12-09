<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill\EbillCollection;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill\EbillCollectionItem;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill\EbillCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill\EbillDetails;
use Pingen\Endpoints\ParameterBags\EbillCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\EbillParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\Exceptions\ValidationException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasOrganisationContext;

/**
 * @package Pingen\Endpoints
 */
class EbillEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;

    /**
     * @param string $ebillId
     * @param ?EbillParameterBag $parameterBag
     * @return EbillDetails
     * @throws RequestException
     */
    public function getDetails(string $ebillId, ?EbillParameterBag $parameterBag = null): EbillDetails
    {
        return new EbillDetails(
            $this->performGetDetailsRequest(
                sprintf('/organisations/%s/deliveries/ebills/%s', $this->getOrganisationId(), $ebillId),
                $parameterBag ?? new EbillParameterBag()
            )->json()
        );
    }

    /**
     * @param ?EbillCollectionParameterBag $ebillCollectionParameterBag
     * @return EbillCollection
     * @throws RateLimitJsonApiException
     * @throws RequestException
     */
    public function getCollection(?EbillCollectionParameterBag $ebillCollectionParameterBag = null): EbillCollection
    {
        return new EbillCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/deliveries/ebills/', $this->getOrganisationId()),
                    $ebillCollectionParameterBag ?? (new EbillCollectionParameterBag())
                )->json()
        );
    }

    /**
     * @param ?EbillCollectionParameterBag $listParameterBag
     * @return \Generator|EbillCollectionItem[]
     * @throws RequestException
     */
    public function iterateOverCollection(?EbillCollectionParameterBag $listParameterBag = null)
    {
        if ($listParameterBag === null) {
            $listParameterBag = new EbillCollectionParameterBag();
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

    /**
     * @param EbillCreateAttributes $ebillCreateAttributes
     * @param resource|string $file File content as string, or resource
     * @param array $relationships
     * @return EbillDetails
     * @throws RequestException
     * @throws ValidationException
     */
    public function uploadAndCreate(EbillCreateAttributes $ebillCreateAttributes, $file, array $relationships = []): EbillDetails
    {
        $ebillCreateAttributes->validate(['file_url', 'file_url_signature']);

        $fileUploadEndpoint = $this->getFileUploadEndpoint();
        if ($this->isUsingStaging()) {
            $fileUploadEndpoint->useStaging();
        }

        $fileUploadDetails = $fileUploadEndpoint->requestFileUpload();
        $fileUploadEndpoint->uploadFile($fileUploadDetails, $file);

        $ebillCreateAttributes
            ->setFileUrl($fileUploadDetails->data->attributes->url)
            ->setFileUrlSignature($fileUploadDetails->data->attributes->url_signature);

        return $this->create($ebillCreateAttributes, $relationships);
    }

    /**
     * @param EbillCreateAttributes $ebillCreateAttributes
     * @param array $relationships
     * @return EbillDetails
     * @throws RequestException
     * @throws ValidationException
     */
    public function create(EbillCreateAttributes $ebillCreateAttributes, array $relationships = []): EbillDetails
    {
        $ebillCreateAttributes->validate();

        return new EbillDetails(
            $this->performPostRequest(
                sprintf('/organisations/%s/deliveries/ebills/', $this->getOrganisationId()),
                'ebills',
                $ebillCreateAttributes,
                $relationships
            )->json()
        );
    }

    protected function getFileUploadEndpoint(): FileUploadEndpoint
    {
        return new FileUploadEndpoint($this->getAccessToken());
    }
}
