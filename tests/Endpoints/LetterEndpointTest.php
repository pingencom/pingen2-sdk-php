<?php

declare(strict_types=1);

namespace Tests\Provider;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\LettersEndpoint;
use Pingen\Endpoints\ParameterBags\LetterCollectionParameterBag;
use Tests\Endpoints\EndpointTest;

/**
 * Class ProviderTest
 * @package Tests
 */
class LetterEndpointTest extends EndpointTest
{
    public function testGetLetterCollection(): void
    {
        $listParameterBag = new LetterCollectionParameterBag();
        $listParameterBag->setPageLimit(10);
        $listParameterBag->setPageNumber(2);

        $endpoint = new LettersEndpoint($this->getAccessToken());
        $endpoint->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/letters/?page%5Blimit%5D=10&page%5Bnumber%5D=2');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
