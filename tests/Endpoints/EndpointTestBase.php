<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use League\OAuth2\Client\Token\AccessToken;
use Tests\TestCase;

/**
 * Class EndpointTestBase
 * @package Tests\Endpoints
 */
abstract class EndpointTestBase extends TestCase
{
    protected function getAccessToken(): AccessToken
    {
        return new AccessToken(['access_token' => 'example']);
    }
}
