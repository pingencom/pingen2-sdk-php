<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\Deliveries\EBill\EBillCollection;
use Pingen\Endpoints\DataTransferObjects\Deliveries\EBill\EBillCollectionItem;
use Pingen\Endpoints\DataTransferObjects\Deliveries\EBill\EBillCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Deliveries\EBill\EBillDetails;
use Pingen\Endpoints\ParameterBags\EBillCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\EBillParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\Exceptions\ValidationException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasOrganisationContext;

/**
 * @package Pingen\Endpoints
 */
class EBillEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;

    /**
     * @param string $ebillId
     * @param ?EBillParameterBag $parameterBag
     * @return EBillDetails
     * @throws RequestException
     */
    public function getDetails(string $ebillId, ?EBillParameterBag $parameterBag = null): EBillDetails
    {
        return new EBillDetails(
            $this->performGetDetailsRequest(
                sprintf('/organisations/%s/deliveries/ebills/%s', $this->getOrganisationId(), $ebillId),
                $parameterBag ?? new EBillParameterBag()
            )->json()
        );
    }

    /**
     * @param ?EBillCollectionParameterBag $ebillCollectionParameterBag
     * @return EBillCollection
     * @throws RateLimitJsonApiException
     * @throws RequestException
     */
    public function getCollection(?EBillCollectionParameterBag $ebillCollectionParameterBag = null): EBillCollection
    {
        return new EBillCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/deliveries/ebills/', $this->getOrganisationId()),
                    $ebillCollectionParameterBag ?? (new EBillCollectionParameterBag())
                )->json()
        );
    }

    /**
     * @param ?EBillCollectionParameterBag $listParameterBag
     * @return \Generator|EBillCollectionItem[]
     * @throws RequestException
     */
    public function iterateOverCollection(?EBillCollectionParameterBag $listParameterBag = null)
    {
        if ($listParameterBag === null) {
            $listParameterBag = new EBillCollectionParameterBag();
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
     * @param EBillCreateAttributes $ebillCreateAttributes
     * @param resource|string $file File content as string, or resource
     * @param array $relationshipPreset
     * @return EBillDetails
     * @throws RequestException
     * @throws ValidationException
     */
    public function uploadAndCreate(EBillCreateAttributes $ebillCreateAttributes, $file, array $relationshipPreset = []): EBillDetails
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

        return $this->create($ebillCreateAttributes, $relationshipPreset);
    }

    /**
     * @param EBillCreateAttributes $ebillCreateAttributes
     * @param array $relationshipPreset
     * @return EBillDetails
     * @throws RequestException
     * @throws ValidationException
     */
    public function create(EBillCreateAttributes $ebillCreateAttributes, array $relationshipPreset = []): EBillDetails
    {
        $ebillCreateAttributes->validate();

        return new EBillDetails(
            $this->performPostRequest(
                sprintf('/organisations/%s/deliveries/ebills/', $this->getOrganisationId()),
                'ebills',
                $ebillCreateAttributes,
                $relationshipPreset
            )->json()
        );
    }

    protected function getFileUploadEndpoint(): FileUploadEndpoint
    {
        return new FileUploadEndpoint($this->getAccessToken());
    }
}
