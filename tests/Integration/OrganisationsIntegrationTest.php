<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use Pingen\Endpoints\ParameterBags\OrganisationCollectionParameterBag;

#[Group('integration')]
class OrganisationsIntegrationTest extends IntegrationTestCase
{
    public function testListOrganisations(): void
    {
        $collection = $this->organisations()->getCollection();

        $this->assertGreaterThanOrEqual(1, count($collection->data));

        foreach ($collection->data as $organisation) {
            $this->assertNotEmpty($organisation->id);
        }
    }

    public function testListOrganisationsPaginated(): void
    {
        $collection = $this->organisations()->getCollection(
            (new OrganisationCollectionParameterBag())
                ->setPageNumber(1)
                ->setPageLimit(5)
        );

        $this->assertLessThanOrEqual(5, count($collection->data));
        $this->assertSame(1, $collection->meta->current_page);
    }

    public function testGetOrganisationById(): void
    {
        $organisationId = $this->organisationId();

        $details = $this->organisations()->getDetails($organisationId);

        $this->assertSame($organisationId, $details->data->id);

        if ($this->organisationName() !== '') {
            $this->assertSame($this->organisationName(), $details->data->attributes->name);
        }
    }
}
