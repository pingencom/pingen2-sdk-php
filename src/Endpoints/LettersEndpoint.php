<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Pingen\Endpoints\DataTransferObjects\Letter\LetterCollection;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterCollectionItem;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterDetails;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterSendAttributes;
use Pingen\Endpoints\ParameterBags\LetterCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\LetterParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasOrganisationContext;

/**
 * Class LettersEndpoint
 * @package Pingen\Endpoints
 */
class LettersEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;

    /**
     * @param string $letterId
     * @param LetterParameterBag|null $parameterBag
     * @return LetterDetails
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getDetails(string $letterId, ?LetterParameterBag $parameterBag = null): LetterDetails
    {
        return new LetterDetails(
            $this->performGetDetailsRequest(
                sprintf('/organisations/%s/letters/%s', $this->getOrganisationId(), $letterId),
                $parameterBag ?? (new LetterParameterBag())
            )->json()
        );
    }

    /**
     * @param LetterCollectionParameterBag|null $letterCollectionParameterBag
     * @return LetterCollection
     * @throws RateLimitJsonApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getCollection(?LetterCollectionParameterBag $letterCollectionParameterBag = null): LetterCollection
    {
        return new LetterCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/letters/', $this->getOrganisationId()),
                    $letterCollectionParameterBag ?? (new LetterCollectionParameterBag())
                )->json()
        );
    }

    /**
     * @param LetterCollectionParameterBag|null $listParameterBag
     * @return \Generator|LetterCollectionItem[]
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function iterateOverCollection(?LetterCollectionParameterBag $listParameterBag = null)
    {
        if ($listParameterBag === null) {
            $listParameterBag = new LetterCollectionParameterBag();
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
     * @param LetterCreateAttributes $letterCreateAttributes
     * @param resource|string $file File content as string, or resource
     * @return LetterDetails
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function uploadAndCreate(LetterCreateAttributes $letterCreateAttributes, $file): LetterDetails
    {
        $fileUploadEndpoint = new FileUploadEndpoint($this->getAccessToken());
        if ($this->isUsingStaging()) {
            $fileUploadEndpoint->useStaging();
        }

        $fileUploadDetails = $fileUploadEndpoint->requestFileUpload();
        $fileUploadEndpoint->uploadFile($fileUploadDetails, $file);

        $letterCreateAttributes
            ->setFileUrl($fileUploadDetails->data->attributes->url)
            ->setFileUrlSignature($fileUploadDetails->data->attributes->url_signature);

        return $this->create($letterCreateAttributes);
    }

    /**
     * @param LetterCreateAttributes $letterCreateAttributes
     * @return LetterDetails
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function create(LetterCreateAttributes $letterCreateAttributes): LetterDetails
    {
        return new LetterDetails(
            $this->performPostRequest(
                sprintf('/organisations/%s/letters/', $this->getOrganisationId()),
                'letters',
                $letterCreateAttributes
            )->json()
        );
    }

    /**
     * @param string $letterId
     * @param LetterSendAttributes $letterSendAttributes
     * @return LetterDetails
     * @throws RateLimitJsonApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function send(string $letterId, LetterSendAttributes $letterSendAttributes): LetterDetails
    {
        return new LetterDetails(
            $this->performPatchRequest(
                sprintf('/organisations/%s/letters/%s/send', $this->getOrganisationId(), $letterId),
                'letters',
                $letterId,
                $letterSendAttributes
            )->json()
        );
    }
}
