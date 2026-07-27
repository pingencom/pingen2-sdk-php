<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\EbillEventsEndpoint;
use Pingen\Endpoints\ParameterBags\EbillEventCollectionParameterBag;

/**
 * Class EbillEventsEndpointTest
 * @package Tests\Endpoints
 */
class EbillEventsEndpointTest extends EndpointTestBase
{
    public function testGetCollection(): void
    {
        $listParameterBag = (new EbillEventCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setFieldsEbill(['status'])
            ->setFieldsEbillEvent(['code'])
            ->setLanguage('en-GB');

        $endpoint = (new EbillEventsEndpoint($this->getAccessToken()))
            ->setOrganisationId('example')
            ->setEbillId('exampleEbillID');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    $endpoint->getResourceBaseUrl() . '/organisations/example/deliveries/ebills/exampleEbillID/events?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bebills%5D=status&fields%5Bdeliverables_events%5D=code&language=en-GB',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetCollectionParsesEvents(): void
    {
        $endpoint = (new EbillEventsEndpoint($this->getAccessToken()))
            ->setOrganisationId('example')
            ->setEbillId('exampleEbillID');

        $endpoint->getHttpClient()->fakeSequence()
            ->push($this->eventCollectionResponse(), Response::HTTP_OK);

        $collection = $endpoint->getCollection();

        $this->assertCount(1, $collection->data);
        $this->assertEquals('deliverables_events', $collection->data[0]->type);
        $this->assertEquals('undeliverable', $collection->data[0]->attributes->code);
        $this->assertEquals('Pingen', $collection->data[0]->attributes->producer);
        $this->assertEquals('ebills', $collection->data[0]->relationships->ebill->data->type);
    }

    private function eventCollectionResponse(): string
    {
        return (string) json_encode([
            'data' => [
                [
                    'id' => '11111111-1111-1111-1111-111111111111',
                    'type' => 'deliverables_events',
                    'attributes' => [
                        'code' => 'undeliverable',
                        'name' => 'Content failed inspection',
                        'producer' => 'Pingen',
                        'location' => '8051 Zürich, CH',
                        'has_image' => false,
                        'data' => ['string'],
                        'emitted_at' => '2020-11-19T09:42:48+0100',
                        'created_at' => '2020-11-19T09:42:48+0100',
                        'updated_at' => '2020-11-19T09:42:48+0100',
                    ],
                    'relationships' => [
                        'ebill' => [
                            'links' => ['related' => 'string'],
                            'data' => [
                                'id' => '22222222-2222-2222-2222-222222222222',
                                'type' => 'ebills',
                            ],
                        ],
                    ],
                    'links' => ['self' => 'string'],
                ],
            ],
            'links' => [
                'first' => 'string',
                'last' => 'string',
                'prev' => null,
                'next' => null,
                'self' => 'string',
            ],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'from' => 1,
                'to' => 1,
                'total' => 1,
            ],
        ]);
    }
}
