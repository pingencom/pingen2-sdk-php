<?php

declare(strict_types=1);

namespace Pingen\Provider;

use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Pingen\ResourceOwner;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Provider
 * @package Pingen
 */
class Pingen extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected string $authBaseUrlProduction = 'https://identity.pingen.com';

    protected string $authBaseUrlStaging = 'https://identity-integration.pingen.com';

    protected bool $useStaging = false;

    /**
     * Constructs the Pingen OAuth 2.0 service provider.
     *
     * @param array $options An array of options to set on this provider.
     *     Options include `clientId`, `clientSecret`, `redirectUri`, `state` and `staging`.
     * @param array $collaborators An array of collaborators that may be used to
     *     override this provider's default behavior. Collaborators include
     *     `grantFactory`, `requestFactory`, and `httpClient`.
     *     Individual providers may introduce more collaborators, as needed.
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        if ($useStaging = Arr::get($options, 'staging')) {
            $this->useStaging();
        }

        parent::__construct(
            $options,
            $collaborators
        );
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
     * @return void
     */
    public function useStaging(): void
    {
        $this->useStaging = true;
    }

    /**
     * Returns the base URL for authorizing a client.
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->getAuthBaseUrl() . '/auth/authorize';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->getAuthBaseUrl() . '/auth/access-tokens';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->getAuthBaseUrl() . '/user';
    }

    /**
     * @return string
     */
    public function getAuthBaseUrl(): string
    {
        return $this->isUsingStaging() === true ? $this->authBaseUrlStaging : $this->authBaseUrlProduction;
    }

    /**
     * @param string $fragment
     * @return AccessToken
     */
    public function getAccessTokenFromImplicitResponse(string $fragment): AccessToken
    {
        parse_str($fragment, $parsedUrl);

        return new AccessToken(
            [
                'access_token' => $parsedUrl['access_token'],
                'expires_in' => $parsedUrl['expires_in'],
            ]
        );
    }

    /**
     * Requests and returns the resource owner of given access token.
     *
     * @param  AccessToken|AccessTokenInterface $token
     * @return ResourceOwner|ResourceOwnerInterface
     */
    public function getResourceOwner(AccessToken $token)
    {
        return parent::getResourceOwner($token);
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * Checks a provider response for errors.
     *
     * @param ResponseInterface $response
     * @param array|string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() === Response::HTTP_BAD_REQUEST) {
            throw new IdentityProviderException(
                sprintf('Bad request (%s)', $response->getBody()),
                $response->getStatusCode(),
                $response->getBody()->getContents()
            );
        }

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new IdentityProviderException(
                sprintf('Unknown error (%s)', $response->getBody()),
                $response->getStatusCode(),
                $response->getBody()->getContents()
            );
        }
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new ResourceOwner($response);
    }

    /**
     * Returns authorization parameters based on provided options.
     *
     * @param  array $options
     * @return array Authorization parameters
     */
    protected function getAuthorizationParameters(array $options)
    {
        $options = parent::getAuthorizationParameters($options);

        /*
         * Approval prompt parameter is currently not supported
         */
        unset($options['approval_prompt']);

        return $options;
    }
}
