<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\LetterEventsEndpoint;
use Pingen\Endpoints\ParameterBags\LetterEventCollectionParameterBag;

/**
 * Class LetterEventsEndpointTest
 * @package Tests
 */
class LetterEventsEndpointTest extends EndpointTest
{
    public function testGetCollection(): void
    {
        $listParameterBag = new LetterEventCollectionParameterBag();
        $listParameterBag->setPageLimit(10);
        $listParameterBag->setPageNumber(2);

        $endpoint = new LetterEventsEndpoint($this->getAccessToken());
        $endpoint->setOrganisationId('example');
        $endpoint->setLetterId('exampleLetterID');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/letters/exampleLetterID/events?page%5Blimit%5D=10&page%5Bnumber%5D=2');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
