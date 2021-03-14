<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use Carbon\Carbon;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadDetails;
use Pingen\Endpoints\FileUploadEndpoint;

/**
 * Class FileUploadTest
 * @package Tests
 */
class FileUploadTest extends EndpointTest
{
    public function testUploadUrlExpired(): void
    {
        $this->expectErrorMessage('File upload url has expired. Please request a new one.');

        $endpoint = new FileUploadEndpoint($this->getAccessToken());

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
    } //@codeCoverageIgnore
}
