<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadAttributes;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadDetails;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadDetailsData;
use Pingen\Endpoints\FileUploadEndpoint;
use Pingen\Endpoints\ParameterBags\FileUploadParameterBag;

class FileUploadTest extends EndpointTest
{
    public function testRequestFileUpload(): void
    {
        $endpoint = new FileUploadEndpoint($this->getAccessToken());
        $endpoint->useStaging();

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new FileUploadDetailsData([
                        'id' => 'someTestId',
                        'type' => 'file_uploads',
                        'attributes' => new FileUploadAttributes([
                            'url' => 'https://s3.example/bucket/filename?signer=url',
                            'url_signature' => '$2y$10$BLOzVbYTXrh4LZbSYNVf7eEDrc58vvQ9PRVZABqV/9WS1eqIcm3M',
                            'expires_at' => '2020-11-19T09:42:48+0100'
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->requestFileUpload(new FileUploadParameterBag());

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    sprintf('%s/file-upload', $endpoint->getResourceBaseUrl()),
                    $request->url()
                );
            }
        );
        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testUploadUrlExpired(): void
    {
       $endpoint = new FileUploadEndpoint($this->getAccessToken());

        try {
            $endpoint->uploadFile(
                new FileUploadDetails(
                    [
                        'data' => [
                            'id' => 'id',
                            'type' => 'file_uploads',
                            'attributes' => [
                                'url' => 'http://exampeurl',
                                'url_signature' => 'examplesignature',
                                'expires_at' => Carbon::now()->subMinute(),
                            ],
                        ],
                    ]
                ),
                'lorem ipsum'
            );
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('File upload url has expired. Please request a new one.', $e->getMessage());
        }
    }

    public function testUploadedFileIsResource(): void
    {
        $endpoint = new FileUploadEndpoint($this->getAccessToken());
        $endpoint->getHttpClient()->fakeSequence()->push('', Response::HTTP_OK);
        /** @var resource $file */
        $file = tmpfile();

        $endpoint->uploadFile(
            new FileUploadDetails(
                [
                    'data' => [
                        'id' => 'id',
                        'type' => 'file_uploads',
                        'attributes' => [
                            'url' => 'http://exampeurl',
                            'url_signature' => 'examplesignature',
                            'expires_at' => Carbon::now()->addDay(),
                        ],
                    ],
                ]
            ),
            $file
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    sprintf('http://exampeurl'),
                    $request->url()
                );
            }
        );
        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testUploadedFileIsString(): void
    {
        $endpoint = new FileUploadEndpoint($this->getAccessToken());
        $endpoint->getHttpClient()->fakeSequence()->push('', Response::HTTP_OK);

        $endpoint->uploadFile(
            new FileUploadDetails(
                [
                    'data' => [
                        'id' => 'id',
                        'type' => 'file_uploads',
                        'attributes' => [
                            'url' => 'http://exampeurl',
                            'url_signature' => 'examplesignature',
                            'expires_at' => Carbon::now()->addDay(),
                        ],
                    ],
                ]
            ),
            'something'
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    sprintf('http://exampeurl'),
                    $request->url()
                );
            }
        );
        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testUploadedFileIsFile(): void
    {
        $endpoint = new FileUploadEndpoint($this->getAccessToken());
        $endpoint->getHttpClient()->fakeSequence()->push('', Response::HTTP_OK);
        /** @var resource $file */
        $file = tmpfile();

        $endpoint->uploadFile(
            new FileUploadDetails(
                [
                    'data' => [
                        'id' => 'id',
                        'type' => 'file_uploads',
                        'attributes' => [
                            'url' => 'http://exampeurl',
                            'url_signature' => 'examplesignature',
                            'expires_at' => Carbon::now()->addDay(),
                        ],
                    ],
                ]
            ),
            stream_get_meta_data($file)['uri']
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    sprintf('http://exampeurl'),
                    $request->url()
                );
            }
        );
        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
