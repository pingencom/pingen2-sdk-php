<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchCollection;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchCollectionItem;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchDetails;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchEditAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchSendAttributes;
use Pingen\Endpoints\ParameterBags\BatchCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\BatchParameterBag;
use Pingen\Exceptions\JsonApiException;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasOrganisationContext;

class BatchesEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;

    /**
     * @param string $batchId
     * @param BatchParameterBag|null $parameterBag
     * @return BatchDetails
     * @throws RequestException
     */
    public function getDetails(string $batchId, ?BatchParameterBag $parameterBag = null): BatchDetails
    {
        return new BatchDetails(
            $this->performGetDetailsRequest(
                sprintf('/organisations/%s/batches/%s', $this->getOrganisationId(), $batchId),
                $parameterBag ?? (new BatchParameterBag())
            )->json()
        );
    }

    /**
     * @param BatchCollectionParameterBag|null $batchCollectionParameterBag
     * @return BatchCollection
     * @throws RateLimitJsonApiException
     * @throws RequestException
     */
    public function getCollection(?BatchCollectionParameterBag $batchCollectionParameterBag = null): BatchCollection
    {
        return new BatchCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/batches/', $this->getOrganisationId()),
                    $batchCollectionParameterBag ?? (new BatchCollectionParameterBag())
                )->json()
        );
    }

    /**
     * @param BatchCollectionParameterBag|null $listParameterBag
     * @return \Generator|BatchCollectionItem[]
     * @throws RequestException
     */
    public function iterateOverCollection(?BatchCollectionParameterBag $listParameterBag = null)
    {
        if ($listParameterBag === null) {
            $listParameterBag = new BatchCollectionParameterBag();
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
     * @param BatchCreateAttributes $batchCreateAttributes
     * @param resource|string $file File content as string, or resource
     * @return BatchDetails
     * @throws RequestException
     */
    public function uploadAndCreate(BatchCreateAttributes $batchCreateAttributes, $file): BatchDetails
    {
        $fileUploadEndpoint = $this->getFileUploadEndpoint();
        if ($this->isUsingStaging()) {
            $fileUploadEndpoint->useStaging();
        }

        $fileUploadDetails = $fileUploadEndpoint->requestFileUpload();
        $fileUploadEndpoint->uploadFile($fileUploadDetails, $file);

        $batchCreateAttributes
            ->setFileUrl($fileUploadDetails->data->attributes->url)
            ->setFileUrlSignature($fileUploadDetails->data->attributes->url_signature);

        return $this->create($batchCreateAttributes);
    }

    /**
     * @param BatchCreateAttributes $batchCreateAttributes
     * @return BatchDetails
     * @throws RequestException
     */
    public function create(BatchCreateAttributes $batchCreateAttributes): BatchDetails
    {
        return new BatchDetails(
            $this->performPostRequest(
                sprintf('/organisations/%s/batches/', $this->getOrganisationId()),
                'batches',
                $batchCreateAttributes
            )->json()
        );
    }

    /**
     * @param string $batchId
     * @param BatchSendAttributes $batchesendAttributes
     * @return BatchDetails
     * @throws JsonApiException
     */
    public function send(string $batchId, BatchSendAttributes $batchesendAttributes): BatchDetails
    {
        return new BatchDetails(
            $this->performPatchRequest(
                sprintf('/organisations/%s/batches/%s/send', $this->getOrganisationId(), $batchId),
                'batches',
                $batchId,
                $batchesendAttributes
            )->json()
        );
    }

    /**
     * @param string $batchId
     * @param BatchEditAttributes $batchEditAttributes
     * @return BatchDetails
     * @throws JsonApiException
     */
    public function edit(string $batchId, BatchEditAttributes $batchEditAttributes): BatchDetails
    {
        return new BatchDetails(
            $this->performPatchRequest(
                sprintf('/organisations/%s/batches/%s', $this->getOrganisationId(), $batchId),
                'batches',
                $batchId,
                $batchEditAttributes
            )->json()
        );
    }

    /**
     * @param string $batchId
     * @return void
     * @throws RateLimitJsonApiException
     * @throws RequestException
     */
    public function cancel(string $batchId): void
    {
        $this->performPatchRequest(
            sprintf('/organisations/%s/batches/%s/cancel', $this->getOrganisationId(), $batchId),
            'batches',
            $batchId
        );
    }

    /**
     * @param string $batchId
     * @return void
     * @throws RateLimitJsonApiException
     * @throws RequestException
     */
    public function delete(string $batchId): void
    {
        $this->performDeleteRequest(
            sprintf('/organisations/%s/batches/%s', $this->getOrganisationId(), $batchId)
        );
    }

    protected function getFileUploadEndpoint(): FileUploadEndpoint
    {
        return new FileUploadEndpoint($this->getAccessToken());
    }
}
