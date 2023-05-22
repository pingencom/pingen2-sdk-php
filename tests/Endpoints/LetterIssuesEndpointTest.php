<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\LetterIssuesEndpoint;
use Pingen\Endpoints\ParameterBags\LetterIssuesCollectionParameterBag;

/**
 * Class LetterIssuesEndpointTest
 * @package Tests
 */
class LetterIssuesEndpointTest extends EndpointTest
{
    public function testGetCollection(): void
    {
        $listParameterBag = (new LetterIssuesCollectionParameterBag())
            ->setPageLimit(10)->setPageNumber(2)
            ->setFieldsLetter(['status'])
            ->setFieldsLetterEvent(['code'])
            ->setLanguage('en-GB');

        $endpoint = (new LetterIssuesEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    $endpoint->getResourceBaseUrl() . '/organisations/example/letters/issues?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bletters%5D=status&fields%5Bletters_events%5D=code&language=en-GB',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
