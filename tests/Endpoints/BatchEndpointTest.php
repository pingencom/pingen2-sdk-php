<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\BatchEndpoint;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchDetailsData;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchEditAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchSendAttributes;
use Pingen\Endpoints\ParameterBags\BatchCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\BatchParameterBag;

class BatchEndpointTest extends EndpointTest
{
    public function testGetBatchCollection(): void
    {
        $listParameterBag = (new BatchCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2);

        $endpoint = (new BatchEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/batches/?page%5Blimit%5D=10&page%5Bnumber%5D=2');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetDetails(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new BatchDetailsData([
                        'id' => $batchId,
                        'type' => 'batches',
                        'attributes' => new BatchAttributes([
                            'name' => 'example',
                            'icon' => 'campaign',
                            'status' => 'sent',
                            'file_original_name' => 'uploaded.zip',
                            'letter_count' => 21,
                            'address_position' => 'left',
                            'price_currency' => 'CHF',
                            'price_value' => 1.25,
                            'delivery_product' => 'fast',
                            'print_mode' => 'simplex',
                            'print_spectrum' => 'color',
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->getDetails($batchId, new BatchParameterBag());

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $batchId, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/batches/%s', $endpoint->getResourceBaseUrl(), $organisationId, $batchId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollection(): void
    {
        $listParameterBag = (new BatchCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2);

        $endpoint = (new BatchEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->iterateOverCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/batches/?page%5Blimit%5D=10&page%5Bnumber%5D=2');
            }
        );

        $this->assertCount(0, $endpoint->getHttpClient()->recorded());
    }

    public function testCreate(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new BatchDetailsData([
                        'id' => $batchId,
                        'type' => 'batches',
                        'attributes' => new BatchAttributes([
                            'name' => 'example',
                            'icon' => 'campaign',
                            'status' => 'sent',
                            'file_original_name' => 'uploaded.zip',
                            'letter_count' => 21,
                            'address_position' => 'left',
                            'price_currency' => 'CHF',
                            'price_value' => 1.25,
                            'delivery_product' => 'fast',
                            'print_mode' => 'simplex',
                            'print_spectrum' => 'color',
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_CREATED);

        $endpoint->create((new BatchCreateAttributes())
            ->setName('example')
            ->setIcon('campaign')
            ->setFileOriginalName('lorem.pdf')
            ->setFileUrl('https =>//objects.cloudscale.ch/bucket/example')
            ->setFileUrlSignature('$2y$10$JpVa0BVfKQmjpDk8MPNujOJ78AM1XLotY.JAjM4HFjpSRjUwqKPfq')
            ->setAddressPosition('left')
            ->setGroupingType('zip')
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/batches/', $endpoint->getResourceBaseUrl(), $organisationId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testSend(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new BatchDetailsData([
                        'id' => $batchId,
                        'type' => 'batches',
                        'attributes' => new BatchAttributes([
                            'name' => 'example',
                            'icon' => 'campaign',
                            'status' => 'sent',
                            'file_original_name' => 'uploaded.zip',
                            'letter_count' => 21,
                            'address_position' => 'left',
                            'price_currency' => 'CHF',
                            'price_value' => 1.25,
                            'delivery_product' => 'fast',
                            'print_mode' => 'simplex',
                            'print_spectrum' => 'color',
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->send($batchId, (new BatchSendAttributes())
            ->setDeliveryProduct('fast')
            ->setPrintMode('simplex')
            ->setPrintSpectrum('color')
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $batchId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/batches/%s/send', $endpoint->getResourceBaseUrl(), $organisationId, $batchId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testEdit(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new BatchDetailsData([
                        'id' => $batchId,
                        'type' => 'batches',
                        'attributes' => new BatchAttributes([
                            'name' => 'edited',
                            'icon' => 'campaign',
                            'status' => 'sent',
                            'file_original_name' => 'uploaded.zip',
                            'letter_count' => 21,
                            'address_position' => 'left',
                            'price_currency' => 'CHF',
                            'price_value' => 1.25,
                            'delivery_product' => 'fast',
                            'print_mode' => 'simplex',
                            'print_spectrum' => 'color',
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->edit($batchId, (new BatchEditAttributes())->setName('edited'));

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $batchId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/batches/%s', $endpoint->getResourceBaseUrl(), $organisationId, $batchId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCancel(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push([], Response::HTTP_ACCEPTED);

        $endpoint->cancel($batchId);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $batchId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/batches/%s/cancel', $endpoint->getResourceBaseUrl(), $organisationId, $batchId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testDelete(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push([], Response::HTTP_NO_CONTENT);

        $endpoint->delete($batchId);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $batchId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/batches/%s', $endpoint->getResourceBaseUrl(), $organisationId, $batchId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
