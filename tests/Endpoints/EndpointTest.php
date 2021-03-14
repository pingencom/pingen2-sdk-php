<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use League\OAuth2\Client\Token\AccessToken;
use Tests\TestCase;

/**
 * Class EndpointTest
 * @package Tests\Endpoints
 */
abstract class EndpointTest extends TestCase
{
    protected function getAccessToken(): AccessToken
    {
        return new AccessToken(['access_token' => 'example']);
    }
}
