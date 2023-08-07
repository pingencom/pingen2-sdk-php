<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Carbon\Carbon;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadAttributes;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadDetailsData;
use Pingen\Endpoints\DataTransferObjects\Letter\AddAttachmentToMultipleLettersAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterAddAttachmentAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterDetailsData;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterEditAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterPriceAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterPriceCalculationAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterPriceData;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterSendAttributes;
use Pingen\Endpoints\FileUploadEndpoint;
use Pingen\Endpoints\LettersEndpoint;
use Pingen\Endpoints\ParameterBags\LetterCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\LetterParameterBag;
use Pingen\Exceptions\JsonApiException;
use Pingen\Exceptions\JsonApiExceptionError;
use Pingen\Exceptions\JsonApiExceptionErrorSource;
use Pingen\Exceptions\RateLimitJsonApiException;

class LetterEndpointTest extends EndpointTest
{
    public function testGetLetterCollection(): void
    {
        $listParameterBag = (new LetterCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setFieldsLetter(['status']);

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/letters/?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bletters%5D=status');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetDetails(): void
    {
        $letterId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new LetterDetailsData([
                        'id' => $letterId,
                        'type' => 'letters',
                        'attributes' => new LetterAttributes([
                            'status' => 'string',
                            'file_original_name' => 'lorem.pdf',
                            'file_pages' => 2,
                            'address' => 'Hans Meier\nExample street 4\n8000 Zürich\nSwitzerland',
                            'address_position' => 'left',
                            'country' => 'CH',
                            'delivery_product' => 'fast',
                            'print_mode' => 'simplex',
                            'print_spectrum' => 'color',
                            'price_currency' => 'CHF',
                            'price_value' => 1.25,
                            'paper_types' => ['normal', 'qr'],
                            'fonts' => [(object)[
                                'name' => 'Helvetica',
                                'is_embedded' => true
                            ]],
                            'tracking_number' => '98.1234.11',
                            'submitted_at' => '2021-11-19T09:42:48+0100',
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->getDetails($letterId, (new LetterParameterBag())->setFields(['status']));

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $letterId, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/%s', $endpoint->getResourceBaseUrl(), $organisationId, $letterId) . '?fields%5Bletters%5D=status',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollection(): void
    {
        $listParameterBag = (new LetterCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setSort('created_at')
            ->setFilter(['name' => 'testName'])
            ->setQ('test')
            ->setInclude(['organisations']);

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        foreach ($endpoint->iterateOverCollection($listParameterBag) as $letterCollectionItem) {
            //
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/letters/?page%5Blimit%5D=10&page%5Bnumber%5D=2&sort=created_at&filter=%7B%22name%22%3A%22testName%22%7D&q=test&include=organisations');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollectionRateLimit(): void
    {
        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(json_encode(['errors' => [
                new JsonApiExceptionError([
                    'code' => (string) Response::HTTP_TOO_MANY_REQUESTS,
                    'title' => 'title',
                    'source' => new JsonApiExceptionErrorSource()
                ])]
            ]),Response::HTTP_TOO_MANY_REQUESTS);

        foreach ($endpoint->iterateOverCollection() as $letterCollectionItem) {
            //
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/letters/');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCreate(): void
    {
        $letterId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new LetterDetailsData([
                        'id' => $letterId,
                        'type' => 'letters',
                        'attributes' => new LetterAttributes([
                            'status' => 'validating',
                            'file_original_name' => 'lorem.pdf',
                            'file_pages' => 2,
                            'address' => 'Hans Meier\nExample street 4\n8000 Zürich\nSwitzerland',
                            'address_position' => 'left',
                            'country' => 'CH',
                            'paper_types' => [],
                            'fonts' => [],
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_CREATED);

        $endpoint->create((new LetterCreateAttributes())
            ->setFileOriginalName('lorem.pdf')
            ->setFileUrl('https =>//objects.cloudscale.ch/bucket/example')
            ->setFileUrlSignature('$2y$10$JpVa0BVfKQmjpDk8MPNujOJ78AM1XLotY.JAjM4HFjpSRjUwqKPfq')
            ->setAddressPosition('left')
            ->setAutoSend(false));

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/', $endpoint->getResourceBaseUrl(), $organisationId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCreateAndUpload(): void
    {
        $letterId = 'exampleId';
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

        $endpoint = $this->createPartialMock(LettersEndpoint::class, ['getFileUploadEndpoint']);
        $endpoint->method('getFileUploadEndpoint')->willReturn($fileUploadEndpoint);
        $endpoint->setAccessToken($this->getAccessToken())
             ->setHttpClient(new HttpClient());
        $endpoint->setOrganisationId($organisationId)
            ->useStaging();

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new LetterDetailsData([
                        'id' => $letterId,
                        'type' => 'letters',
                        'attributes' => new LetterAttributes([
                            'status' => 'validating',
                            'file_original_name' => 'lorem.pdf',
                            'file_pages' => 2,
                            'address' => 'Hans Meier\nExample street 4\n8000 Zürich\nSwitzerland',
                            'address_position' => 'left',
                            'country' => 'CH',
                            'paper_types' => [],
                            'fonts' => [],
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_CREATED);

        /** @var resource $file */
        $file = tmpfile();

        $endpoint->uploadAndCreate((new LetterCreateAttributes())
            ->setFileOriginalName('lorem.pdf')
            ->setAddressPosition('left')
            ->setAutoSend(false), $file);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/', $endpoint->getResourceBaseUrl(), $organisationId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCreateAndUploadUnauthorized(): void
    {
        $organisationId = 'orgId';

        $endpoint = new LettersEndpoint($this->getAccessToken());
        $endpoint->setOrganisationId($organisationId);

        /** @var resource $file */
        $file = tmpfile();

        try {
            $endpoint->uploadAndCreate((new LetterCreateAttributes())
                ->setFileOriginalName('lorem.pdf')
                ->setAddressPosition('left')
                ->setAutoSend(false), $file);
        } catch (JsonApiException $e) {
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $e->getCode());
        }
    }

    public function testSend(): void
    {
        $letterId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new LetterDetailsData([
                        'id' => $letterId,
                        'type' => 'letters',
                        'attributes' => new LetterAttributes([
                            'status' => 'submitted',
                            'file_original_name' => 'lorem.pdf',
                            'file_pages' => 2,
                            'address' => 'Hans Meier\nExample street 4\n8000 Zürich\nSwitzerland',
                            'address_position' => 'left',
                            'country' => 'CH',
                            'delivery_product' => 'fast',
                            'print_mode' => 'simplex',
                            'print_spectrum' => 'color',
                            'price_currency' => 'CHF',
                            'price_value' => 1.25,
                            'paper_types' => ['normal', 'qr'],
                            'fonts' => [(object)[
                                'name' => 'Helvetica',
                                'is_embedded' => true
                            ]],
                            'submitted_at' => '2021-11-19T09:42:48+0100',
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->send($letterId, (new LetterSendAttributes())
            ->setDeliveryProduct('fast')
            ->setPrintMode('simplex')
            ->setPrintSpectrum('color')
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $letterId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/%s/send', $endpoint->getResourceBaseUrl(), $organisationId, $letterId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testEdit(): void
    {
        $letterId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new LetterDetailsData([
                        'id' => $letterId,
                        'type' => 'letters',
                        'attributes' => new LetterAttributes([
                            'status' => 'valid',
                            'file_original_name' => 'lorem.pdf',
                            'file_pages' => 2,
                            'address' => 'Hans Meier\nExample street 4\n8000 Zürich\nSwitzerland',
                            'address_position' => 'left',
                            'country' => 'CH',
                            'paper_types' => ['normal', 'normal'],
                            'fonts' => [(object)[
                                'name' => 'Helvetica',
                                'is_embedded' => true
                            ]],
                            'created_at' => '2020-11-19T09:42:48+0100',
                            'updated_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->edit($letterId, (new LetterEditAttributes())->setPaperTypes(['normal', 'normal']));

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $letterId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/%s', $endpoint->getResourceBaseUrl(), $organisationId, $letterId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCancel(): void
    {
        $letterId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push([], Response::HTTP_ACCEPTED);

        $endpoint->cancel($letterId);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $letterId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/%s/cancel', $endpoint->getResourceBaseUrl(), $organisationId, $letterId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testDelete(): void
    {
        $letterId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push([], Response::HTTP_NO_CONTENT);

        $endpoint->delete($letterId);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $letterId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/%s', $endpoint->getResourceBaseUrl(), $organisationId, $letterId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCalculatePrice(): void
    {
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(json_encode([
                'data' => new LetterPriceData([
                    'type' => 'letter_price_calculator',
                    'attributes' => new LetterPriceAttributes([
                        'currency' => 'CHF',
                        'price' => 2.01
                    ])
                ])
            ]),Response::HTTP_OK);

        $endpoint->calculatePrice((new LetterPriceCalculationAttributes())
            ->setPrintSpectrum('color')
            ->setPrintMode('simplex')
            ->setDeliveryProduct('fast')
            ->setPaperTypes(['normal', 'qr'])
            ->setCountry('CH')
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/price-calculator', $endpoint->getResourceBaseUrl(), $organisationId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetFile(): void
    {
        $letterId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push('',Response::HTTP_FOUND);

        $endpoint->getFile($letterId);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $letterId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/%s/file', $endpoint->getResourceBaseUrl(), $organisationId, $letterId),
                    $request->url()
                );
            }
        );
    }

    public function testRateLimit(): void
    {
        $letterId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(json_encode(['errors' => [
                new JsonApiExceptionError([
                    'code' => (string) Response::HTTP_TOO_MANY_REQUESTS,
                    'title' => 'title',
                    'source' => new JsonApiExceptionErrorSource()
                ])]
            ]),Response::HTTP_TOO_MANY_REQUESTS);

        try {
            $endpoint->getFile($letterId);
        } catch (RateLimitJsonApiException $e) {
            $this->assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $e->getCode());
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $letterId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/%s/file', $endpoint->getResourceBaseUrl(), $organisationId, $letterId),
                    $request->url()
                );
            }
        );
    }

    public function testApiException(): void
    {
        $letterId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(json_encode(['errors' => [
                new JsonApiExceptionError([
                    'code' => (string) Response::HTTP_FORBIDDEN,
                    'title' => 'title',
                    'source' => new JsonApiExceptionErrorSource()
                ])]
            ]),Response::HTTP_FORBIDDEN);

        try {
            $endpoint->getFile($letterId);
        } catch (JsonApiException $e) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $e->getCode());
            $this->assertEquals(1, count($e->getBody()->errors));
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $letterId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/%s/file', $endpoint->getResourceBaseUrl(), $organisationId, $letterId),
                    $request->url()
                );
            }
        );
    }

    public function testAddAttachment(): void
    {
        $letterId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push([], Response::HTTP_ACCEPTED);

        $endpoint->addAttachment($letterId, (new LetterAddAttachmentAttributes())
            ->setFileUrl('https =>//objects.cloudscale.ch/bucket/example')
            ->setFileUrlSignature('$2y$10$JpVa0BVfKQmjpDk8MPNujOJ78AM1XLotY.JAjM4HFjpSRjUwqKPfq')
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId, $letterId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/%s/attachment', $endpoint->getResourceBaseUrl(), $organisationId, $letterId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testAddAttachmentToMultipleLetters(): void
    {
        $letterIdA = 'exampleIdA';
        $letterIdB = 'exampleIdB';
        $organisationId = 'orgId';

        $endpoint = (new LettersEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push([], Response::HTTP_ACCEPTED);

        $endpoint->addAttachmentToMultipleLetters((new AddAttachmentToMultipleLettersAttributes())
            ->setLetterIds([$letterIdA, $letterIdB])
            ->setFileUrl('https =>//objects.cloudscale.ch/bucket/example')
            ->setFileUrlSignature('$2y$10$JpVa0BVfKQmjpDk8MPNujOJ78AM1XLotY.JAjM4HFjpSRjUwqKPfq')
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/letters/attachment', $endpoint->getResourceBaseUrl(), $organisationId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
