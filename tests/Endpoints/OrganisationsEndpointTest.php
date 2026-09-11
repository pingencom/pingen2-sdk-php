<?php

namespace Tests\Endpoints;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationAttributes;
use Pingen\Endpoints\OrganisationsEndpoint;
use Pingen\Endpoints\ParameterBags\OrganisationCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\OrganisationParameterBag;
use Pingen\Exceptions\JsonApiExceptionError;
use Pingen\Exceptions\JsonApiExceptionErrorSource;

class OrganisationsEndpointTest extends EndpointTestBase
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

        $endpoint->getDetails($orgId, (new OrganisationParameterBag())->setFields(['name']));

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $orgId): void {
                $this->assertEquals(
                    sprintf('%s/organisations/%s', $endpoint->getResourceBaseUrl(), $orgId) . '?fields%5Bcompanies%5D=name',
                    $request->url());
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetCollection(): void
    {
        $endpoint = new OrganisationsEndpoint($this->getAccessToken());

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                '{"data": [], "links": {"first": "string", "last": "string", "prev": null, "next": null, "self": "string"}, "meta": {"current_page": 2, "last_page": 3, "per_page": 10, "from": 10, "to": 19, "total": 30}}',
                Response::HTTP_OK
            );

        $endpoint->getCollection(
            (new OrganisationCollectionParameterBag())
                ->setPageLimit(10)
                ->setPageNumber(2)
                ->setFieldsOrganisation(['name'])
        );

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    $endpoint->getResourceBaseUrl() . '/organisations?page%5Blimit%5D=10&page%5Bnumber%5D=2&fields%5Borganisations%5D=name',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollection(): void
    {
        $endpoint = new OrganisationsEndpoint($this->getAccessToken());

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => [
                        [
                            'id' => 'orgId',
                            'type' => 'organisations',
                            'attributes' => new OrganisationAttributes([
                                'name' => 'ACME GmbH',
                                'status' => 'active',
                                'plan' => 'free',
                                'billing_mode' => 'prepaid',
                                'billing_currency' => 'CHF',
                                'billing_balance' => 11.23,
                                'default_country' => 'CH',
                                'default_address_position' => 'left',
                                'data_retention_addresses' => 18,
                                'data_retention_pdf' => 12,
                                'color' => '#0758FF',
                                'created_at' => '2020-11-19T09:42:48+0100',
                                'updated_at' => '2020-11-19T09:42:48+0100'
                            ])
                        ]
                    ],
                    'links' => ['first' => 'string', 'last' => 'string', 'prev' => null, 'next' => null, 'self' => 'string'],
                    'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 10, 'from' => 1, 'to' => 1, 'total' => 1]
                ]),
                Response::HTTP_OK
            );

        $ids = [];

        foreach ($endpoint->iterateOverCollection() as $organisation) {
            $ids[] = $organisation->id;
        }

        $this->assertEquals(['orgId'], $ids);
        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollectionRateLimit(): void
    {
        $endpoint = new OrganisationsEndpoint($this->getAccessToken());

        $endpoint->getHttpClient()->fakeSequence()
            ->push(json_encode(['errors' => [
                new JsonApiExceptionError([
                    'code' => (string) Response::HTTP_TOO_MANY_REQUESTS,
                    'title' => 'title',
                    'source' => new JsonApiExceptionErrorSource()
                ])]
            ]), Response::HTTP_TOO_MANY_REQUESTS);

        foreach ($endpoint->iterateOverCollection() as $organisation) {
            //
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals($endpoint->getResourceBaseUrl() . '/organisations', $request->url());
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
