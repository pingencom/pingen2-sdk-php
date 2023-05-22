<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\BatchEventsEndpoint;
use Pingen\Endpoints\ParameterBags\BatchEventCollectionParameterBag;

class BatchEventsEndpointTest extends EndpointTest
{
    public function testGetCollection(): void
    {
        $listParameterBag = (new BatchEventCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setFieldsBatch(['name'])
            ->setFieldsBatchEvent(['code'])
            ->setLanguage('en-GB');

        $endpoint = (new BatchEventsEndpoint($this->getAccessToken()))
            ->setOrganisationId('example')
            ->setBatchId('exampleLetterID');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    $endpoint->getResourceBaseUrl() . '/organisations/example/batches/exampleLetterID/events?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bbatches%5D=name&fields%5Bbatches_events%5D=code&language=en-GB',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
