<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Pingen\Endpoints\DataTransferObjects\Letter\LetterCollection;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterCollectionItem;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterDetails;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterEditAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterSendAttributes;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookCollection;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookCollectionItem;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookDetails;
use Pingen\Endpoints\ParameterBags\LetterCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\LetterParameterBag;
use Pingen\Endpoints\ParameterBags\WebhookCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\WebhookParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasOrganisationContext;

/**
 * Class WebhooksEndpoint
 * @package Pingen\Endpoints
 */
class WebhooksEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;

    /**
     * @param string $webhookId
     * @param WebhookParameterBag|null $parameterBag
     * @return LetterDetails
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getDetails(string $webhookId, ?WebhookParameterBag $parameterBag = null): WebhookDetails
    {
        return new WebhookDetails(
            $this->performGetDetailsRequest(
                sprintf('/organisations/%s/webhooks/%s', $this->getOrganisationId(), $webhookId),
                $parameterBag ?? (new WebhookParameterBag())
            )->json()
        );
    }

    /**
     * @param WebhookCollectionParameterBag|null $webhookCollectionParameterBag
     * @return WebhookCollection
     * @throws RateLimitJsonApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getCollection(?WebhookCollectionParameterBag $webhookCollectionParameterBag = null): WebhookCollection
    {
        return new WebhookCollection(
            $this
                ->performGetCollectionRequest(
                    sprintf('/organisations/%s/webhooks', $this->getOrganisationId()),
                    $webhookCollectionParameterBag ?? (new WebhookCollectionParameterBag())
                )->json()
        );
    }

    /**
     * @param WebhookCollectionParameterBag|null $listParameterBag
     * @return \Generator|WebhookCollectionItem[]
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function iterateOverCollection(?WebhookCollectionParameterBag $listParameterBag = null)
    {
        if ($listParameterBag === null) {
            $listParameterBag = new WebhookCollectionParameterBag();
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
     * @param WebhookCreateAttributes $webhookCreateAttributes
     * @return WebhookDetails
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function create(WebhookCreateAttributes $webhookCreateAttributes): WebhookDetails
    {
        return new WebhookDetails(
            $this->performPostRequest(
                sprintf('/organisations/%s/webhooks', $this->getOrganisationId()),
                'webhooks',
                $webhookCreateAttributes
            )->json()
        );
    }

    /**
     * @param string $webhookId
     * @return void
     * @throws RateLimitJsonApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function delete(string $webhookId): void
    {
        $this->performDeleteRequest(
            sprintf('/organisations/%s/webhooks/%s', $this->getOrganisationId(), $webhookId)
        );
    }
}
