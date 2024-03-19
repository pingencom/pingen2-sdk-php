<?php

namespace Tests\Endpoints;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Pingen\Endpoints\DataTransferObjects\General\CollectionLinks;
use Pingen\Endpoints\DataTransferObjects\General\CollectionMeta;
use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItem;
use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItemData;
use Pingen\Endpoints\DataTransferObjects\General\RelationshipRelatedItemLinks;
use Pingen\Endpoints\DataTransferObjects\UserAssociation\UserAssociationAttributes;
use Pingen\Endpoints\DataTransferObjects\UserAssociation\UserAssociationDetailsData;
use Pingen\Endpoints\DataTransferObjects\UserAssociation\UserAssociationRelationships;
use Pingen\Endpoints\DataTransferObjects\UserAssociation\UserAssociationsCollectionItem;
use Pingen\Endpoints\ParameterBags\UserAssociationCollectionParameterBag;
use Pingen\Endpoints\ParameterBags\UserAssociationParameterBag;
use Pingen\Endpoints\UserAssociationsEndpoint;
use Pingen\Exceptions\JsonApiExceptionError;
use Pingen\Exceptions\JsonApiExceptionErrorSource;

class UserAssociationsEndpointTest extends EndpointTestBase
{
    public function testGetCollection(): void
    {
        $endpoint = new UserAssociationsEndpoint($this->getAccessToken());

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => [
                        new UserAssociationsCollectionItem([
                            'id' => 'associationId',
                            'type' => 'associations',
                            'attributes' => new UserAssociationAttributes([
                                'role' => 'owner',
                                'status' => 'active',
                                'created_at' => '2023-03-01T12:12:00+0100',
                                'updated_at' => '2023-03-01T12:12:00+0100'
                            ]),
                        ])
                    ],
                    'links' => new CollectionLinks([
                        'first' => 'string',
                        'last' => 'string',
                        'prev' => null,
                        'next' => null,
                        'self' => 'string'
                    ]),
                    'meta' => new CollectionMeta([
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 10,
                        'from' => 1,
                        'to' => 9,
                        'total' => 0
                    ])
                ]),
                Response::HTTP_OK,
            );

        $endpoint->getCollection((new UserAssociationCollectionParameterBag())->setFieldsAssociation(['role'])->setFieldsOrganisations(['name']));

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    sprintf( '%s/user/associations', $endpoint->getResourceBaseUrl()) . '?fields%5Bassociations%5D=role&fields%5Borganisations%5D=name',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollection(): void
    {
        $endpoint = new UserAssociationsEndpoint($this->getAccessToken());

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => [
                        new UserAssociationsCollectionItem([
                            'id' => 'associationId',
                            'type' => 'associations',
                            'attributes' => new UserAssociationAttributes([
                                'role' => 'owner',
                                'status' => 'active',
                                'created_at' => '2023-03-01T12:12:00+0100',
                                'updated_at' => '2023-03-01T12:12:00+0100'
                            ]),
                        ])
                    ],
                    'links' => new CollectionLinks([
                        'first' => 'string',
                        'last' => 'string',
                        'prev' => null,
                        'next' => null,
                        'self' => 'string'
                    ]),
                    'meta' => new CollectionMeta([
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 10,
                        'from' => 1,
                        'to' => 9,
                        'total' => 0
                    ])
                ]),Response::HTTP_OK);

        foreach ($endpoint->iterateOverCollection(new UserAssociationCollectionParameterBag()) as $userCollectionItem) {
            //
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    sprintf( '%s/user/associations', $endpoint->getResourceBaseUrl()),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testIterateOverCollectionRateLimit(): void
    {
        $endpoint = new UserAssociationsEndpoint($this->getAccessToken());

        $endpoint->getHttpClient()->fakeSequence()
            ->push(json_encode(['errors' => [
                new JsonApiExceptionError([
                    'code' => (string) Response::HTTP_TOO_MANY_REQUESTS,
                    'title' => 'title',
                    'source' => new JsonApiExceptionErrorSource()
                ])]
            ]),Response::HTTP_TOO_MANY_REQUESTS);

        foreach ($endpoint->iterateOverCollection() as $userCollectionItem) {
            //
        }

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    sprintf( '%s/user/associations', $endpoint->getResourceBaseUrl()),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }

    public function testGetDetails(): void
    {
        $endpoint = new UserAssociationsEndpoint($this->getAccessToken());
        $associationId = 'associationId';

        $endpoint->getHttpClient()->fakeSequence()
            ->push(
                json_encode([
                    'data' => new UserAssociationDetailsData([
                        'id' => $associationId,
                        'type' => 'associations',
                        'attributes' => new UserAssociationAttributes([
                            'role' => 'owner',
                            'status' => 'active',
                            'created_at' => '2023-03-01T12:12:00+0100',
                            'updated_at' => '2023-03-01T12:12:00+0100'
                        ]),
                        'relationships' => new UserAssociationRelationships([
                            'organisation' => new RelationshipRelatedItem([
                                'links' => new RelationshipRelatedItemLinks([
                                    'related' => 'string'
                                ]),
                                'data' => new RelationshipRelatedItemData([
                                    'id' => 'orgId',
                                    'type' => 'organisations'
                                ])
                            ])
                        ])
                    ])
                ]),Response::HTTP_OK);

        $endpoint->getDetails($associationId, (new UserAssociationParameterBag())->setFields(['role']));

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $associationId): void {
                $this->assertEquals(
                    sprintf( '%s/user/associations/%s', $endpoint->getResourceBaseUrl(), $associationId) . '?fields%5Busers%5D=role',
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}
