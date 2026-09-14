<?php

declare(strict_types=1);

namespace Tests\Endpoints;

use League\OAuth2\Client\Token\AccessToken;
use Pingen\Exceptions\ValidationException;
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

    protected function assertValidationFails(callable $callback, string ...$expectedMessages): void
    {
        $message = null;

        try {
            $callback();
        } catch (ValidationException $exception) {
            $message = $exception->getMessage();
        }

        $this->assertNotNull($message, 'Expected a ValidationException.');

        foreach ($expectedMessages as $expected) {
            $this->assertStringContainsString($expected, (string) $message);
        }
    }
}
