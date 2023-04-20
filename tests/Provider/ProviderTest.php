<?php

declare(strict_types=1);

namespace Tests\Provider;

use GuzzleHttp\Psr7\Stream;
use League\OAuth2\Client\Token\AccessToken;
use Mockery as m;
use Pingen\Provider\Pingen;
use Tests\TestCase;

class ProviderTest extends TestCase
{
    protected Pingen $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new Pingen(
            [
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'redirectUri' => 'none',
                'staging' => true,
            ]
        );
    }

    public function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }

    public function testAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayNotHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }

    public function testResourceOwnerDetailsUrl(): void
    {
        $url = $this->provider->getResourceOwnerDetailsUrl(new AccessToken([
            'access_token' => 'test',
        ]));

        $this->assertEquals($this->provider->getAuthBaseUrl() . '/user', $url);
    }

    public function testClientCredentialsAccessTokenIssued(): void
    {
        $stream = fopen('data://text/plain,{"token_type": "Bearer", "access_token": "mock_access_token", "expires_in": 3600}','r');

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn(new Stream($stream));
        $response->shouldReceive('getHeader')->andReturn([
            'content-type' => 'json',
        ]);
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);

        $this->provider->setHttpClient($client);

        $accessToken = $this->provider->getAccessToken('client_credentials');

        $this->assertEquals($accessToken->getToken(), 'mock_access_token');
        $this->assertNull($accessToken->getRefreshToken());
    }

    public function testGetAccessTokenFromImplicitResponseFragment(): void
    {
        $accessToken = $this->provider->getAccessTokenFromImplicitResponse('access_token=mock_access_token&token_type=Bearer&expires_in=43200&state=yourrandomstate');

        $this->assertEquals($accessToken->getToken(), 'mock_access_token');
        $this->assertNull($accessToken->getRefreshToken());
    }

    public function testGetResourceOwner(): void
    {
        $stream = fopen('data://text/plain,{"data": {"id": "uuidv4", "attributes": {"email": "foo@bar", "first_name": "Foo", "last_name": "Bar"}}}','r');
        $userResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $userResponse->shouldReceive('getBody')->andReturn(new Stream($stream));
        $userResponse->shouldReceive('getHeader')->andReturn([
            'content-type' => 'json',
        ]);
        $userResponse->shouldReceive('getStatusCode')->andReturn(200);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($userResponse);

        $this->provider->setHttpClient($client);

        $resourceOwner = $this->provider->getResourceOwner(new AccessToken([
            'access_token' => 'test',
        ]));
        $arrayResourceOwner = $resourceOwner->toArray();

        $this->assertEquals($arrayResourceOwner, [
            'id' => 'uuidv4',
            'email' => 'foo@bar',
            'name' => 'Foo Bar',
        ]);
        $this->assertEquals('uuidv4', $resourceOwner->getId());
        $this->assertEquals('Foo Bar', $resourceOwner->getFullName());
        $this->assertEquals('foo@bar', $resourceOwner->getEmail());
    }
}
