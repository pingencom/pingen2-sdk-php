<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookCollection;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookCollectionItem;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookDetails;
use Pingen\Endpoints\ParameterBags\WebhookCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\WebhookParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;
use Pingen\Support\HasOrganisationContext;

class WebhookEndpoint extends ResourceEndpoint
{
    use HasOrganisationContext;

    /**
     * @param string $webhookId
     * @param WebhookParameterBag|null $parameterBag
     * @return WebhookDetails
     * @throws RequestException
     * @throws RateLimitJsonApiException
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
     * @throws RequestException
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
     * @throws RequestException
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
     * @throws RequestException
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
     * @throws RequestException
     */
    public function delete(string $webhookId): void
    {
        $this->performDeleteRequest(
            sprintf('/organisations/%s/webhooks/%s', $this->getOrganisationId(), $webhookId)
        );
    }
}
