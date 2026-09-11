<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class UserIntegrationTest extends IntegrationTestCase
{
    public function testGetUser(): void
    {
        $user = $this->users()->getDetails();

        $this->assertNotEmpty($user->data->id);
        $this->assertNotEmpty($user->data->attributes->email);
    }

    public function testGetUserAssociations(): void
    {
        $collection = $this->userAssociations()->getCollection();

        $this->assertGreaterThanOrEqual(0, count($collection->data));
    }
}
