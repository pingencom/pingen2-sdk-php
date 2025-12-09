<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailCollection;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailCollectionItem;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailDetails;
use Pingen\Endpoints\ParameterBags\EmailCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\EmailParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\Exceptions\ValidationException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasOrganisationContext;

/**
 * @package Pingen\Endpoints
 */
class EmailEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;

    /**
     * @param string $emailId
     * @param ?EmailParameterBag $parameterBag
     * @return EmailDetails
     * @throws RequestException
     */
    public function getDetails(string $emailId, ?EmailParameterBag $parameterBag = null): EmailDetails
    {
        return new EmailDetails(
            $this->performGetDetailsRequest(
                sprintf('/organisations/%s/deliveries/emails/%s', $this->getOrganisationId(), $emailId),
                $parameterBag ?? new EmailParameterBag()
            )->json()
        );
    }

    /**
     * @param ?EmailCollectionParameterBag $emailCollectionParameterBag
     * @return EmailCollection
     * @throws RateLimitJsonApiException
     * @throws RequestException
     */
    public function getCollection(?EmailCollectionParameterBag $emailCollectionParameterBag = null): EmailCollection
    {
        return new EmailCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/deliveries/emails/', $this->getOrganisationId()),
                    $emailCollectionParameterBag ?? (new EmailCollectionParameterBag())
                )->json()
        );
    }

    /**
     * @param ?EmailCollectionParameterBag $listParameterBag
     * @return \Generator|EmailCollectionItem[]
     * @throws RequestException
     */
    public function iterateOverCollection(?EmailCollectionParameterBag $listParameterBag = null)
    {
        if ($listParameterBag === null) {
            $listParameterBag = new EmailCollectionParameterBag();
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
     * @param EmailCreateAttributes $emailCreateAttributes
     * @param resource|string $file File content as string, or resource
     * @param array $relationships
     * @return EmailDetails
     * @throws RequestException
     * @throws ValidationException
     */
    public function uploadAndCreate(EmailCreateAttributes $emailCreateAttributes, $file, array $relationships = []): EmailDetails
    {
        $emailCreateAttributes->validate(['file_url', 'file_url_signature']);

        $fileUploadEndpoint = $this->getFileUploadEndpoint();
        if ($this->isUsingStaging()) {
            $fileUploadEndpoint->useStaging();
        }

        $fileUploadDetails = $fileUploadEndpoint->requestFileUpload();
        $fileUploadEndpoint->uploadFile($fileUploadDetails, $file);

        $emailCreateAttributes
            ->setFileUrl($fileUploadDetails->data->attributes->url)
            ->setFileUrlSignature($fileUploadDetails->data->attributes->url_signature);

        return $this->create($emailCreateAttributes, $relationships);
    }

    /**
     * @param EmailCreateAttributes $emailCreateAttributes
     * @param array $relationships
     * @return EmailDetails
     * @throws RequestException
     * @throws ValidationException
     */
    public function create(EmailCreateAttributes $emailCreateAttributes, array $relationships = []): EmailDetails
    {
        $emailCreateAttributes->validate();

        return new EmailDetails(
            $this->performPostRequest(
                sprintf('/organisations/%s/deliveries/emails/', $this->getOrganisationId()),
                'emails',
                $emailCreateAttributes,
                $relationships
            )->json()
        );
    }

    protected function getFileUploadEndpoint(): FileUploadEndpoint
    {
        return new FileUploadEndpoint($this->getAccessToken());
    }
}
