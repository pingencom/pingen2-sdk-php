<?php

namespace Tests\Endpoints;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationAttributes;
use Pingen\Endpoints\OrganisationsEndpoint;
use Pingen\Endpoints\ParameterBags\OrganisationParameterBag;

class OrganisationsEndpointTest extends EndpointTest
{
    public function testGetDetails(): void
    {
        $endpoint = new OrganisationsEndpoint($this->getAccessToken());
        $orgId = 'orgId';

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => [
                        'id' => $orgId,
                        'type' => 'organisations',
                        'attributes' => new OrganisationAttributes([
                            "name"=> "ACME GmbH",
                            "status"=> "active",
                            "plan"=> "free",
                            "billing_mode"=> "prepaid",
                            "billing_currency"=> "CHF",
                            "billing_balance"=> 11.23,
                            "default_country"=> "CH",
                            "default_address_position"=> "left",
                            "data_retention_addresses"=> 18,
                            "data_retention_pdf"=> 12,
                            "color"=> "#0758FF",
                            "created_at"=> "2020-11-19T09:42:48+0100",
                            "updated_at"=> "2020-11-19T09:42:48+0100"
                        ]),
                        'relationships' => []
                    ]
                ]),Response::HTTP_OK);

        $endpoint->getDetails($orgId, new OrganisationParameterBag());

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $orgId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s', $endpoint->getResourceBaseUrl(), $orgId),
                    $request->url());
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}