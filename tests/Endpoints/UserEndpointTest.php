<?php

namespace Tests\Endpoints;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\DataTransferObjects\User\UserAttributes;
use Pingen\Endpoints\ParameterBags\UserParameterBag;
use Pingen\Endpoints\UserEndpoint;

class UserEndpointTest extends EndpointTest
{
    public function testGetDetails(): void
    {
        $endpoint = new UserEndpoint($this->getAccessToken());

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => [
                        'id' => 'userId',
                        'type' => 'users',
                        'attributes' => new UserAttributes([
                            'email' => 'test@example.com',
                            'first_name' => 'John',
                            'last_name' => 'Snow',
                            'status' => 'active',
                            'language' => 'en-GB',
                            'created_at' => '2023-03-01T12:12:00+0100',
                            'updated_at' => '2023-03-01T12:12:00+0100'
                        ]),
                    ]
                ]),
                Response::HTTP_OK,
            );

        $endpoint->getDetails(new UserParameterBag());

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($request->url(), $endpoint->getResourceBaseUrl() . '/user');
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}