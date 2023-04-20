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

class UserAssociationsEndpointTest extends EndpointTest
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

        $endpoint->getCollection(new UserAssociationCollectionParameterBag());

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

        $endpoint->iterateOverCollection(new UserAssociationCollectionParameterBag());

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint): void {
                $this->assertEquals(
                    sprintf( '%s/user/associations', $endpoint->getResourceBaseUrl()),
                    $request->url()
                );
            }
        );

        $this->assertCount(0, $endpoint->getHttpClient()->recorded());
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

        $endpoint->getDetails($associationId, new UserAssociationParameterBag());

        $endpoint->getHttpClient()->recorded(
            function (Request $request) use ($endpoint, $associationId): void {
                $this->assertEquals(
                    sprintf( '%s/user/associations/%s', $endpoint->getResourceBaseUrl(), $associationId),
                    $request->url()
                );
            }
        );

        $this->assertCount(1, $endpoint->getHttpClient()->recorded());
    }
}