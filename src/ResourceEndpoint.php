<?php

declare(strict_types=1);

namespace Pingen;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use League\OAuth2\Client\Token\AccessToken;
use Pingen\Exceptions\JsonApiException;
use Pingen\Exceptions\RateLimitJsonApiException;
use Pingen\Support\CollectionParameterBag;
use Pingen\Support\DataTransferObject\DataTransferObject;
use Pingen\Support\Input;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class Endpoint
 * @package Pingen
 */
abstract class ResourceEndpoint
{
    /**
     * Default timeout of http requests in seconds
     */
    public const DEFAULT_REQUEST_TIMEOUT = 20;

    protected AccessToken $accessToken;

    protected HttpClient $httpClient;

    protected string $resourceBaseUrlProduction = 'https://api.pingen.com';

    protected string $resourceBaseUrlStaging = 'https://api-staging.pingen.com';

    protected bool $useStaging = false;

    protected ?string $idempotencyKey = null;

    /**
     * Endpoint constructor.
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->setAccessToken($accessToken);
        $this->setHttpClient(new HttpClient());

        DataTransferObject::$makers[CarbonImmutable::class] = fn ($value): ?CarbonImmutable => CarbonImmutable::make($value);
    }

    /**
     * Use staging instead of production
     *
     * @return bool
     */
    public function isUsingStaging(): bool
    {
        return $this->useStaging;
    }

    /**
     * @return static
     */
    public function useStaging(): self
    {
        $this->useStaging = true;

        return $this;
    }

    public function setIdempotencyKey(string $key): self
    {
        $this->idempotencyKey = $key;

        return $this;
    }

    /**
     * @param AccessToken $accessToken
     * @return ResourceEndpoint
     */
    public function setAccessToken(AccessToken $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getResourceBaseUrl(): string
    {
        return $this->isUsingStaging() === true ? $this->resourceBaseUrlStaging : $this->resourceBaseUrlProduction;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * @param HttpClient $httpClient
     * @return void
     */
    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $endpoint
     * @param CollectionParameterBag $collectionParameterBag
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     * @throws RateLimitJsonApiException
     */
    protected function performGetCollectionRequest(
        string $endpoint,
        CollectionParameterBag $collectionParameterBag
    ): Response {
        /** @var Response $response */
        $response = $this->getAuthenticatedJsonApiRequest()
            ->get(
                $this->getResourceBaseUrl() . $endpoint,
                $collectionParameterBag->all()
            );

        return $this->setOnErrorCallbackForJsonApiResponses($response);
    }

    /**
     * @param string $endpoint
     * @param ParameterBag $parameterBag
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     * @throws RateLimitJsonApiException
     */
    protected function performGetDetailsRequest(string $endpoint, ParameterBag $parameterBag): Response
    {
        /** @var Response $response */
        $response = $this->getAuthenticatedJsonApiRequest()
            ->get(
                $this->getResourceBaseUrl() . $endpoint,
                $parameterBag->all()
            );

        return $this->setOnErrorCallbackForJsonApiResponses($response);
    }

    /**
     * @param string $endpoint
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     * @throws RateLimitJsonApiException
     */
    protected function performGetRequest(string $endpoint): Response
    {
        /** @var Response $response */
        $response = $this->getAuthenticatedJsonApiRequest()
            ->get(
                $this->getResourceBaseUrl() . $endpoint
            );

        return $this->setOnErrorCallbackForJsonApiResponses($response);
    }

    /**
     * @param string $endpoint
     * @param string $type
     * @param Input $body
     * @param array $relationships
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     * @throws RateLimitJsonApiException
     */
    protected function performPostRequest(string $endpoint, string $type, Input $body, array $relationships = [])
    {
        /** @var Response $response */
        $response = $this->getAuthenticatedJsonApiRequest()
            ->post(
                $this->getResourceBaseUrl() . $endpoint,
                [
                    'data' => [
                        'type' => $type,
                        'attributes' => $body->toArray(),
                        'relationships' => $relationships,
                    ],
                ]
            );

        return $this->setOnErrorCallbackForJsonApiResponses($response);
    }

    /**
     * @param string $endpoint
     * @return Response
     * @throws JsonApiException
     */
    protected function performDeleteRequest(string $endpoint)
    {
        /** @var Response $response */
        $response = $this->getAuthenticatedJsonApiRequest()
            ->delete(
                $this->getResourceBaseUrl() . $endpoint
            );

        return $this->setOnErrorCallbackForJsonApiResponses($response);
    }

    /**
     * @param string $endpoint
     * @param string $type
     * @param string $id
     * @param Input|null $body
     * @return Response
     * @throws JsonApiException
     */
    protected function performPatchRequest(string $endpoint, string $type, string $id, ?Input $body = null): Response
    {
        $data = [];

        if ($body !== null) {
            $data = [
                'data' => [
                    'id' => $id,
                    'type' => $type,
                    'attributes' => $body->toArray(),
                ],
            ];
        }
        /** @var Response $response */
        $response = $this->getAuthenticatedJsonApiRequest()
            ->patch(
                $this->getResourceBaseUrl() . $endpoint,
                $data
            );

        return $this->setOnErrorCallbackForJsonApiResponses($response);
    }

    /**
     * @param string $url
     * @param resource $tmpFile
     * @return void
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function performPutFileRequest(string $url, $tmpFile): void
    {
        /** @var Response $response */
        $response =  $this->getHttpClient()
            ->bodyFormat('none')
            ->withOptions([
                'body' => $tmpFile,
                'headers' => [
                    'Content-Type' => 'application/pdf',
                ],
            ])
            ->put($url);

        $response->throw();
    }

    /**
     * @return PendingRequest
     */
    protected function getAuthenticatedJsonApiRequest(): PendingRequest
    {
        $pendingRequest = $this->getAuthenticatedRequest()
            ->accept('application/vnd.api+json')
            ->contentType('application/vnd.api+json');

        if ($this->idempotencyKey !== null) {
            $pendingRequest = $pendingRequest->withHeaders([
                'Idempotency-Key' => $this->idempotencyKey,
            ]);
        }

        return $pendingRequest;
    }

    /**
     * @return PendingRequest
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    protected function getAuthenticatedRequest(): PendingRequest
    {
        return $this->getHttpClient()
            ->timeout(self::DEFAULT_REQUEST_TIMEOUT)
            ->withUserAgent('PINGEN.SDK.PHP')
            ->withToken($this->getAccessToken()->getToken());
    }

    /**
     * @param Response $response
     * @return Response
     * @throws JsonApiException
     */
    protected function setOnErrorCallbackForJsonApiResponses(Response $response): Response
    {
        return $response->onError(
            function (Response $response): void {
                if ($response->status() === \Symfony\Component\HttpFoundation\Response::HTTP_TOO_MANY_REQUESTS) {
                    throw new RateLimitJsonApiException($response);
                }
                throw new JsonApiException($response);
            }
        );
    }
}
