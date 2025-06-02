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
class LetterEventsEndpointTest extends EndpointTestBase
{
    public function testGetCollection(): void
    {
        $listParameterBag = (new LetterEventCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setFieldsLetter(['status'])
            ->setFieldsLetterEvent(['code'])
            ->setLanguage('en-GB');

        $endpoint = (new LetterEventsEndpoint($this->getAccessToken()))
            ->setOrganisationId('example')
            ->setLetterId('exampleLetterID');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    $endpoint->getResourceBaseUrl() . '/organisations/example/letters/exampleLetterID/events?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bletters%5D=status&fields%5Bletters_events%5D=code&language=en-GB',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetIssuesCollection(): void
    {
        $listParameterBag = (new LetterEventCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setFieldsLetter(['status'])
            ->setFieldsLetterEvent(['code'])
            ->setLanguage('en-GB');

        $endpoint = (new LetterEventsEndpoint($this->getAccessToken()))
            ->setOrganisationId('example')
            ->setLetterId('exampleLetterID');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getIssuesCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    $endpoint->getResourceBaseUrl() . '/organisations/example/letters/events/issues?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bletters%5D=status&fields%5Bletters_events%5D=code&language=en-GB',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetSentCollection(): void
    {
        $listParameterBag = (new LetterEventCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setFieldsLetter(['status'])
            ->setFieldsLetterEvent(['code'])
            ->setLanguage('en-GB');

        $endpoint = (new LetterEventsEndpoint($this->getAccessToken()))
            ->setOrganisationId('example')
            ->setLetterId('exampleLetterID');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getSentCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    $endpoint->getResourceBaseUrl() . '/organisations/example/letters/events/sent?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bletters%5D=status&fields%5Bletters_events%5D=code&language=en-GB',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetUndeliverableCollection(): void
    {
        $listParameterBag = (new LetterEventCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setFieldsLetter(['status'])
            ->setFieldsLetterEvent(['code'])
            ->setLanguage('en-GB');

        $endpoint = (new LetterEventsEndpoint($this->getAccessToken()))
            ->setOrganisationId('example')
            ->setLetterId('exampleLetterID');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getUndeliverableCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    $endpoint->getResourceBaseUrl() . '/organisations/example/letters/events/undeliverable?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bletters%5D=status&fields%5Bletters_events%5D=code&language=en-GB',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetDeliveredCollection(): void
    {
        $listParameterBag = (new LetterEventCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setFieldsLetter(['status'])
            ->setFieldsLetterEvent(['code'])
            ->setLanguage('en-GB');

        $endpoint = (new LetterEventsEndpoint($this->getAccessToken()))
            ->setOrganisationId('example')
            ->setLetterId('exampleLetterID');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getUndeliverableCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    $endpoint->getResourceBaseUrl() . '/organisations/example/letters/events/delivered?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bletters%5D=status&fields%5Bletters_events%5D=code&language=en-GB',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
