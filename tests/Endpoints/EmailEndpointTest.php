<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Carbon\Carbon;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailAttributes;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailDetailsData;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailMetaDataAttributes;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadAttributes;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadDetailsData;
use Pingen\Endpoints\EmailEndpoint;
use Pingen\Endpoints\FileUploadEndpoint;
use Pingen\Endpoints\ParameterBags\EmailCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\EmailParameterBag;
use Pingen\Exceptions\JsonApiException;
use Pingen\Exceptions\JsonApiExceptionError;
use Pingen\Exceptions\JsonApiExceptionErrorSource;

class EmailEndpointTest extends EndpointTestBase
{
    public function testGetEmailCollection(): void
    {
        $listParameterBag = (new EmailCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setFieldsEmail(['status']);

        $endpoint = (new EmailEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        $endpoint->getCollection($listParameterBag);

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/deliveries/emails/?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Bemails%5D=status');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetDetails(): void
    {
        $emailId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new EmailEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new EmailDetailsData([
                        'id' => $emailId,
                        'type' => 'emails',
                        'attributes' => new EmailAttributes([
                            'status' => 'valid',
                            'file_original_name' => 'lorem.pdf',
                            'file_pages' => 2,
                            'recipient_identifier' => 'info@example.com',
                            'price_currency' => 'CHF',
                            'price_value' => 1.25,
                            'source' => 'api',
                            'submitted_at' => '2025-10-19T19:42:48+0100',
                            'created_at' => '2025-10-19T09:42:48+0100',
                            'updated_at' => '2025-10-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->getDetails($emailId, (new EmailParameterBag())->setFields(['status']));

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $emailId, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/deliveries/emails/%s', $endpoint->getResourceBaseUrl(), $organisationId, $emailId) . '?fields%5Bemails%5D=status',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollection(): void
    {
        $listParameterBag = (new EmailCollectionParameterBag())
            ->setPageLimit(10)
            ->setPageNumber(2)
            ->setSort('created_at')
            ->setFilter(['status' => 'valid'])
            ->setQ('test')
            ->setInclude(['organisations']);

        $endpoint = (new EmailEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK,
            );

        foreach ($endpoint->iterateOverCollection($listParameterBag) as $emailCollectionItem) {
            //
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/deliveries/emails/?page%5Blimit%5D=10&page%5Bnumber%5D=2&sort=created_at&filter=%7B%22status%22%3A%22valid%22%7D&q=test&include=organisations');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollectionRateLimit(): void
    {
        $endpoint = (new EmailEndpoint($this->getAccessToken()))
            ->setOrganisationId('example');

        $endpoint->getHttpClient()->fakeSequence()
            ->push(json_encode(['errors' => [
                new JsonApiExceptionError([
                    'code' => (string) Response::HTTP_TOO_MANY_REQUESTS,
                    'title' => 'title',
                    'source' => new JsonApiExceptionErrorSource()
                ])]
            ]),Response::HTTP_TOO_MANY_REQUESTS);

        foreach ($endpoint->iterateOverCollection() as $emailCollectionItem) {
            //
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/organisations/example/deliveries/emails/');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCreate(): void
    {
        $emailId = 'exampleId';
        $organisationId = 'orgId';

        $endpoint = (new EmailEndpoint($this->getAccessToken()))
            ->setOrganisationId($organisationId);

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new EmailDetailsData([
                        'id' => $emailId,
                        'type' => 'emails',
                        'attributes' => new EmailAttributes([
                            'status' => 'validating',
                            'file_original_name' => 'lorem.pdf',
                            'file_pages' => 2,
                            'recipient_identifier' => 'info@example.com',
                            'source' => 'api',
                            'submitted_at' => null,
                            'created_at' => '2025-10-19T09:42:48+0100',
                            'updated_at' => '2025-10-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_CREATED);

        $metaData = (new EmailMetaDataAttributes())
            ->setSenderName('Fake Name')
            ->setRecipientEmail('fake.email@email.com')
            ->setRecipientName('Fake Name')
            ->setReplyEmail('fake.reply.email@email.com')
            ->setReplyName('Fake Name')
            ->setSubject('Fake Subject')
            ->setContent('Some Test Content');

        $endpoint->create((new EmailCreateAttributes())
            ->setFileOriginalName('lorem.pdf')
            ->setFileUrl('https =>//objects.cloudscale.ch/bucket/example')
            ->setFileUrlSignature('$2y$10$JpVa0BVfKQmjpDk8MPNujOJ78AM1XLotY.JAjM4HFjpSRjUwqKPfq')
            ->setAutoSend(false)
            ->setMetaData($metaData));

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/deliveries/emails/', $endpoint->getResourceBaseUrl(), $organisationId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCreateAndUpload(): void
    {
        $emailId = 'exampleId';
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

        $endpoint = $this->createPartialMock(EmailEndpoint::class, ['getFileUploadEndpoint']);
        $endpoint->method('getFileUploadEndpoint')->willReturn($fileUploadEndpoint);
        $endpoint->setAccessToken($this->getAccessToken())
             ->setHttpClient(new HttpClient());
        $endpoint->setOrganisationId($organisationId)
            ->useStaging();

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new EmailDetailsData([
                        'id' => $emailId,
                        'type' => 'emails',
                        'attributes' => new EmailAttributes([
                            'status' => 'validating',
                            'file_original_name' => 'lorem.pdf',
                            'file_pages' => 2,
                            'recipient_identifier' => 'info@example.com',
                            'source' => 'api',
                            'submitted_at' => null,
                            'created_at' => '2025-10-19T09:42:48+0100',
                            'updated_at' => '2025-10-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_CREATED);

        /** @var resource $file */
        $file = tmpfile();

        $endpoint->uploadAndCreate((new EmailCreateAttributes())
            ->setFileOriginalName('lorem.pdf')
            ->setAutoSend(true),
            $file,
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $organisationId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s/deliveries/emails/', $endpoint->getResourceBaseUrl(), $organisationId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testCreateAndUploadUnauthorized(): void
    {
        $organisationId = 'orgId';

        $endpoint = new EmailEndpoint($this->getAccessToken());
        $endpoint->setOrganisationId($organisationId);

        /** @var resource $file */
        $file = tmpfile();

        try {
            $endpoint->uploadAndCreate((new EmailCreateAttributes())
                ->setFileOriginalName('lorem.pdf')
                ->setAutoSend(false), $file);
        } catch (JsonApiException $e) {
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $e->getCode());
        }
    }
}
