<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Carbon\Carbon;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\BatchesEndpoint;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchAddAttachmentAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchDetailsData;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchEbillSendAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchEditAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchEmailSendAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchPostSendAttributes;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadAttributes;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadDetailsData;
use Pingen\Endpoints\FileUploadEndpoint;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchStatisticsAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchStatisticsData;
use Pingen\Endpoints\ParameterBags\BatchCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\BatchParameterBag;
use Pingen\Exceptions\JsonApiException;
use Pingen\Exceptions\JsonApiExceptionError;
use Pingen\Exceptions\JsonApiExceptionErrorSource;

class BatchEndpointTest extends EndpointTestBase
{
    public function testGetBatchCollection(): void
    {
        $listParameterBag = (new BatchCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setFieldsBatch(['name']);

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/batches/?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bbatches%5D=name');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetDetails(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
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
                            'delivery_products' => [
                                'country' => 'CH',
                                'delivery_product' => 'fast'
                            ],
                            'print_mode' => 'simplex',
                            'print_spectrum' => 'color',
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->getDetails($batchId, (new BatchParameterBag())->setFields(['name']));

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $batchId, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/batches/%s', $endpoint->getResourceBaseUrl(), $organisationId, $batchId) . '?fields%5Bbatches%5D=name',
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

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        foreach ($endpoint->iterateOverCollection($listParameterBag) as $batchCollectionItem) {
            //
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/batches/?page%5Blimit%5D=10&page%5Bnumber%5D=2');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollectionRateLimit(): void
    {
        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(json_encode(['errors' => [
                new JsonApiExceptionError([
                    'code' => (string) Response::HTTP_TOO_MANY_REQUESTS,
                    'title' => 'title',
                    'source' => new JsonApiExceptionErrorSource()
                ])]
            ]),Response::HTTP_TOO_MANY_REQUESTS);

        foreach ($endpoint->iterateOverCollection() as $batchCollectionItem) {
            //
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/batches/');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCreate(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
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
                            'delivery_products' => [
                                'country' => 'CH',
                                'delivery_product' => 'fast'
                            ],
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

    public function testCreateValidation(): void
    {
        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('orgId');

        $this->assertValidationFails(
            fn () => $endpoint->create(new BatchCreateAttributes()),
            'The name field is required.',
            'The icon field is required.',
            'The file_original_name field is required.',
            'The file_url field is required.',
            'The file_url_signature field is required.',
            'The address_position field is required.',
            'The grouping_type field is required.',
        );
    }

    public function testCreateAndUpload(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $fileUploadEndpoint = new FileUploadEndpoint($this->getAccessToken());
        $fileUploadEndpoint->getHttpClient()->fakeSequence()->push(
            json_encode([
                'data' => new FileUploadDetailsData([
                    'id' => 'someTestId',
                    'type' => 'file_uploads',
                    'attributes' => new FileUploadAttributes([
                        'url' => 'https://s3.example/bucket/filename?signer=url',
                        'url_signature' => '$2y$10$BLOzVbYTXrh4LZbSYNVf7eEDrc58vvQ9PRVZABqV/9WS1eqIcm3M',
                        'expires_at' => Carbon::now()->addDay()
                    ])
                ])
            ]),Response::HTTP_OK)
            ->push('', Response::HTTP_OK);

        $endpoint = $this->createPartialMock(BatchesEndpoint::class, ['getFileUploadEndpoint']);
        $endpoint->method('getFileUploadEndpoint')->willReturn($fileUploadEndpoint);
        $endpoint->setAccessToken($this->getAccessToken())
            ->setHttpClient(new HttpClient());
        $endpoint->setOrganisationId($organisationId)
            ->useStaging();

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
                            'delivery_products' => [
                                'country' => 'CH',
                                'delivery_product' => 'fast'
                            ],
                            'print_mode' => 'simplex',
                            'print_spectrum' => 'color',
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_CREATED);

        /** @var resource $file */
        $file = tmpfile();

        $endpoint->uploadAndCreate((new BatchCreateAttributes())
            ->setName('example')
            ->setIcon('campaign')
            ->setAddressPosition('left')
            ->setGroupingType('zip')
            ->setFileOriginalName('lorem.pdf')
            ->setAddressPosition('left'), $file);

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

    public function testCreateAndUploadUnauthorized(): void
    {
        $organisationId = 'orgId';

        $endpoint = new BatchesEndpoint($this->getAccessToken());
        $endpoint->setOrganisationId($organisationId)
            ->useStaging();

        /** @var resource $file */
        $file = tmpfile();

        try {
            $endpoint->uploadAndCreate((new BatchCreateAttributes())
                ->setName('example')
                ->setIcon('campaign')
                ->setGroupingType('zip')
                ->setFileOriginalName('lorem.pdf')
                ->setAddressPosition('left'), $file);
        } catch (JsonApiException $e) {
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $e->getCode());
        }
    }

    public function testSend(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
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
                            'delivery_products' => [
                                'country' => 'CH',
                                'delivery_product' => 'fast'
                            ],
                            'print_mode' => 'simplex',
                            'print_spectrum' => 'color',
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->send($batchId, (new BatchPostSendAttributes())
            ->setDeliveryProduct('cheap')
            ->setPrintMode('simplex')
            ->setPrintSpectrum('color')
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $batchId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/batches/%s/send', $endpoint->getResourceBaseUrl(), $organisationId, $batchId),
                    $request->url()
                );
                $this->assertEquals(BatchPostSendAttributes::TYPE, $request->data()['data']['type']);
                $this->assertEquals(
                    [
                        'delivery_product' => 'cheap',
                        'print_mode' => 'simplex',
                        'print_spectrum' => 'color',
                    ],
                    $request->data()['data']['attributes']
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testSendEmailChannel(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push($this->batchDetailsResponse($batchId, 'email'), Response::HTTP_OK);

        $endpoint->send($batchId, new BatchEmailSendAttributes());

        $endpoint->getHttpClient()->recorded(
            function (Request $request): void {
                $this->assertEquals(BatchEmailSendAttributes::TYPE, $request->data()['data']['type']);
                $this->assertEquals(
                    ['delivery_product' => 'electronic_email'],
                    $request->data()['data']['attributes']
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testSendEbillChannel(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push($this->batchDetailsResponse($batchId, 'ebill'), Response::HTTP_OK);

        $endpoint->send($batchId, new BatchEbillSendAttributes());

        $endpoint->getHttpClient()->recorded(
            function (Request $request): void {
                $this->assertEquals(BatchEbillSendAttributes::TYPE, $request->data()['data']['type']);
                $this->assertEquals(
                    ['delivery_product' => 'electronic_ebill'],
                    $request->data()['data']['attributes']
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testSendPostChannelValidation(): void
    {
        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('orgId');

        $this->assertValidationFails(
            fn () => $endpoint->send('exampleId', (new BatchPostSendAttributes())
                ->setDeliveryProduct('electronic_email')
                ->setPrintMode('triplex')
                ->setPrintSpectrum('sepia')),
            'The delivery_product field must be one of: fast, cheap, bulk, premium, registered.',
            'The print_mode field must be one of: simplex, duplex.',
            'The print_spectrum field must be one of: color, grayscale.',
        );
    }

    public function testSendEmailChannelRejectsForeignDeliveryProduct(): void
    {
        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('orgId');

        $this->assertValidationFails(
            fn () => $endpoint->send('exampleId', (new BatchEmailSendAttributes())->setDeliveryProduct('cheap')),
            'The delivery_product field must be electronic_email.',
        );
    }

    public function testSendEbillChannelRejectsForeignDeliveryProduct(): void
    {
        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('orgId');

        $this->assertValidationFails(
            fn () => $endpoint->send('exampleId', (new BatchEbillSendAttributes())->setDeliveryProduct('cheap')),
            'The delivery_product field must be electronic_ebill.',
        );
    }

    public function testEdit(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
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
                            'delivery_products' => [
                                'country' => 'CH',
                                'delivery_product' => 'fast'
                            ],
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

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
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

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
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
                $this->assertEquals(
                    [
                        'id' => $batchId,
                        'type' => 'batches',
                        'attributes' => ['with_letters' => false, 'with_deliverables' => false],
                    ],
                    $request->data()['data']
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetStatistics(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new BatchStatisticsData([
                        'id' => $batchId,
                        'type' => 'batches',
                        'attributes' => new BatchStatisticsAttributes([
                            'letter_validating' => 1,
                            'letter_groups' => [
                                [
                                    'name' => 'validating',
                                    'count' => 1
                                ],
                                [
                                    'name' => 'valid',
                                    'count' => 10
                                ]
                            ],
                            'letter_countries' => [
                                [
                                    'country' => 'CH',
                                    'count' => 5
                                ],
                                [
                                    'country' => 'DE',
                                    'count' => 6
                                ]
                            ]
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->getStatistics($batchId, new BatchParameterBag());

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $batchId, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/batches/%s/statistics', $endpoint->getResourceBaseUrl(), $organisationId, $batchId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testAddAttachment(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push([], Response::HTTP_ACCEPTED);

        $endpoint->addAttachment($batchId, (new BatchAddAttachmentAttributes())
            ->setFileUrl('https =>//objects.cloudscale.ch/bucket/example')
            ->setFileUrlSignature('$2y$10$JpVa0BVfKQmjpDk8MPNujOJ78AM1XLotY.JAjM4HFjpSRjUwqKPfq')
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $batchId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/batches/%s/attachment', $endpoint->getResourceBaseUrl(), $organisationId, $batchId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCreateWithChannelType(): void
    {
        $batchId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push($this->batchDetailsResponse($batchId, 'ebill'), Response::HTTP_CREATED);

        $endpoint->create((new BatchCreateAttributes())
            ->setName('example batch')
            ->setIcon('rocket')
            ->setChannelType('ebill')
            ->setFileOriginalName('lorem.pdf')
            ->setFileUrl('https =>//objects.cloudscale.ch/bucket/example')
            ->setFileUrlSignature('$2y$10$JpVa0BVfKQmjpDk8MPNujOJ78AM1XLotY.JAjM4HFjpSRjUwqKPfq')
            ->setAddressPosition('left')
            ->setGroupingType('zip')
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request): void {
                $this->assertEquals('ebill', $request->data()['data']['attributes']['channel_type']);
                $this->assertEquals('rocket', $request->data()['data']['attributes']['icon']);
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCreateRejectsUnknownChannelTypeAndIcon(): void
    {
        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('orgId');

        $this->assertValidationFails(
            fn () => $endpoint->create((new BatchCreateAttributes())
                ->setName('example batch')
                ->setIcon('unicorn')
                ->setChannelType('carrier-pigeon')
                ->setFileOriginalName('lorem.pdf')
                ->setFileUrl('https =>//objects.cloudscale.ch/bucket/example')
                ->setFileUrlSignature('$2y$10$JpVa0BVfKQmjpDk8MPNujOJ78AM1XLotY.JAjM4HFjpSRjUwqKPfq')
                ->setAddressPosition('left')
                ->setGroupingType('zip')),
            'The icon field must be one of: campaign, megaphone',
            'The channel_type field must be one of: post, ebill, email.',
        );
    }

    public function testCreateRejectsTooShortName(): void
    {
        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('orgId');

        $this->assertValidationFails(
            fn () => $endpoint->create((new BatchCreateAttributes())
                ->setName('nope')
                ->setIcon('campaign')
                ->setFileOriginalName('lorem.pdf')
                ->setFileUrl('https =>//objects.cloudscale.ch/bucket/example')
                ->setFileUrlSignature('$2y$10$JpVa0BVfKQmjpDk8MPNujOJ78AM1XLotY.JAjM4HFjpSRjUwqKPfq')
                ->setAddressPosition('left')
                ->setGroupingType('zip')),
            'The name field must be between 5 and 100 characters.',
        );
    }

    public function testEditRejectsTooShortName(): void
    {
        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('orgId');

        $this->assertValidationFails(
            fn () => $endpoint->edit('exampleId', (new BatchEditAttributes())->setName('nope')),
            'The name field must be between 5 and 100 characters.',
        );
    }

    public function testEditRejectsUnknownIcon(): void
    {
        $endpoint = (new BatchesEndpoint($this->getAccessToken()))
            ->setOrganisationId('orgId');

        $this->assertValidationFails(
            fn () => $endpoint->edit('exampleId', (new BatchEditAttributes())->setIcon('unicorn')),
            'The icon field must be one of:',
        );
    }

    private function batchDetailsResponse(string $batchId, ?string $channelType = null): string
    {
        return (string) json_encode([
            'data' => new BatchDetailsData([
                'id' => $batchId,
                'type' => 'batches',
                'attributes' => new BatchAttributes([
                    'name' => 'example batch',
                    'icon' => 'campaign',
                    'channel_type' => $channelType,
                    'status' => 'sent',
                    'file_original_name' => 'uploaded.zip',
                    'letter_count' => 21,
                    'address_position' => 'left',
                    'price_currency' => 'CHF',
                    'price_value' => 1.25,
                    'print_mode' => 'simplex',
                    'print_spectrum' => 'color',
                    'created_at' => '2020-11-19T09:42:48+0100',
                    'updated_at' => '2020-11-19T09:42:48+0100'
                ])
            ])
        ]);
    }
}
