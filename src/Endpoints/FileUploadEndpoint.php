<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use InvalidArgumentException;
use Pingen\Endpoints\DataTransferObjects\FileUpload\FileUploadDetails;
use Pingen\Endpoints\ParameterBags\FileUploadParameterBag;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\ResourceEndpoint;
use RuntimeException;

/**
 * Class FileUploadsEndpoint
 * @package Pingen\Endpoints
 */
class FileUploadEndpoint extends ResourceEndpoint
{
    /**
     * @param FileUploadParameterBag|null $fileUploadParameterBag
     * @return FileUploadDetails
     * @throws RequestException
     * @throws RateLimitJsonApiException
     */
    public function requestFileUpload(?FileUploadParameterBag $fileUploadParameterBag = null): FileUploadDetails
    {
        return new FileUploadDetails(
            $this
                ->performGetDetailsRequest(
                    '/file-upload',
                    $fileUploadParameterBag ?? (new FileUploadParameterBag())
                )
                ->json()
        );
    }

    /**
     * @param FileUploadDetails $fileUploadDetails
     * @param resource|string $file You can pass here resource, string with path or file contents.
     * @throws RequestException
     * @throws InvalidArgumentException In case url is expired or file cannot be processed.
     * @throws RateLimitJsonApiException
     */
    public function uploadFile(FileUploadDetails $fileUploadDetails, $file): void
    {
        if ($fileUploadDetails->data->attributes->expires_at->isPast()) {
            throw new InvalidArgumentException('File upload url has expired. Please request a new one.');
        }

        $tmpFile = tmpfile();
        if (! is_resource($tmpFile)) {
            throw new RuntimeException('Cannot create tmp file.'); // @codeCoverageIgnore
        }

        switch (true) {
            case is_resource($file):
                stream_copy_to_stream($file, $tmpFile);
                break;
            case is_file($file):
                $tmp = fopen($file, 'r');
                if (! is_resource($tmp)) {
                    throw new RuntimeException('Cannot open file with given path: ' . $tmpFile); // @codeCoverageIgnore
                }
                stream_copy_to_stream($tmp, $tmpFile);
                fclose($tmp);
                break;
            case is_string($file):
                fwrite($tmpFile, $file);
                break;
            default:
                throw new InvalidArgumentException('Invalid file parameter.'); // @codeCoverageIgnore
        }

        $this->performPutFileRequest($fileUploadDetails->data->attributes->url, $tmpFile);
    }
}
