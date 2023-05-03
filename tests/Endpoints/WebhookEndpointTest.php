<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookAttributes;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookDetailsData;
use Pingen\Endpoints\ParameterBags\WebhookCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\WebhookParameterBag;
use Pingen\Endpoints\WebhooksEndpoint;

class WebhookEndpointTest extends EndpointTest
{
    public function testGetWebhookCollection(): void
    {
        $listParameterBag = (new WebhookCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2);

        $endpoint = (new WebhooksEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/webhooks?page%5Blimit%5D=10&page%5Bnumber%5D=2');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetDetails(): void
    {
        $webhookId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new WebhooksEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new WebhookDetailsData([
                        'id' => $webhookId,
                        'type' => 'webhooks',
                        'attributes' => new WebhookAttributes([
                            'event_category' => 'issues',
                            'url' => 'https://example.webhook',
                            'signing_key' => 'someSecretKEy123',
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->getDetails($webhookId, new WebhookParameterBag());

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $webhookId, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/webhooks/%s', $endpoint->getResourceBaseUrl(), $organisationId, $webhookId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollection(): void
    {
        $listParameterBag = (new WebhookCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2);

        $endpoint = (new WebhooksEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->iterateOverCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/webhooks/?page%5Blimit%5D=10&page%5Bnumber%5D=2');
            }
        );

        $this->assertCount(0, $endpoint->getHttpClient()->recorded());
    }

    public function testCreate(): void
    {
        $webhookId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new WebhooksEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new WebhookDetailsData([
                        'id' => $webhookId,
                        'type' => 'webhooks',
                        'attributes' => new WebhookAttributes([
                            'event_category' => 'issues',
                            'url' => 'https://example.webhook',
                            'signing_key' => 'someSecretKEy123',
                        ])
                    ])
                ]),Response::HTTP_CREATED);

        $endpoint->create((new WebhookCreateAttributes())
            ->setEventCategory('issues')
            ->setUrl('https://example.webhook')
            ->setSigningKey('someSecretKEy123')
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/webhooks', $endpoint->getResourceBaseUrl(), $organisationId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testDelete(): void
    {
        $webhookId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new WebhooksEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push([], Response::HTTP_NO_CONTENT);

        $endpoint->delete($webhookId);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $webhookId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/webhooks/%s', $endpoint->getResourceBaseUrl(), $organisationId, $webhookId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
