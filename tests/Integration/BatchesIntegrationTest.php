<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchEditAttributes;
use Pingen\Endpoints\ParameterBags\BatchCollectionParameterBag;
use Pingen\Exceptions\JsonApiException;

#[Group('integration')]
class BatchesIntegrationTest extends IntegrationTestCase
{
    public function testListBatches(): void
    {
        $collection = $this->batches()->getCollection();

        $this->assertGreaterThanOrEqual(0, count($collection->data));
    }

    public function testListBatchesPaginated(): void
    {
        $collection = $this->batches()->getCollection(
            (new BatchCollectionParameterBag())
                ->setPageNumber(1)
                ->setPageLimit(3)
        );

        $this->assertLessThanOrEqual(3, count($collection->data));
    }

    public function testCreateBatch(): string
    {
        $details = $this->batches()->uploadAndCreate(
            $this->batchCreateAttributes(self::FILE_NAME),
            $this->document()
        );

        $this->assertNotEmpty($details->data->id);
        $this->assertSame('post', $details->data->attributes->channel_type);

        return $details->data->id;
    }

    #[Depends('testCreateBatch')]
    public function testGetBatchById(string $batchId): void
    {
        $details = $this->batches()->getDetails($batchId);

        $this->assertSame($batchId, $details->data->id);
        $this->assertNotEmpty($details->data->attributes->status);
    }

    #[Depends('testCreateBatch')]
    public function testUpdateBatch(string $batchId): void
    {
        // Give the batch time to reach an updatable state.
        $this->waitFor(10);

        $details = $this->batches()->edit(
            $batchId,
            (new BatchEditAttributes())
                ->setName('Updated Integration Batch')
                ->setIcon('rocket')
        );

        $this->assertSame('Updated Integration Batch', $details->data->attributes->name);
        $this->assertSame('rocket', $details->data->attributes->icon);
    }

    #[Depends('testCreateBatch')]
    public function testGetBatchEvents(string $batchId): void
    {
        $collection = $this->batchEvents($batchId)->getCollection();

        $this->assertGreaterThanOrEqual(0, count($collection->data));
    }

    #[Depends('testCreateBatch')]
    public function testGetBatchStatistics(string $batchId): void
    {
        $statistics = $this->batches()->getStatistics($batchId);

        $this->assertSame($batchId, $statistics->data->id);
    }

    public function testCreateBatchForDeletion(): string
    {
        $details = $this->batches()->uploadAndCreate(
            $this->batchCreateAttributes(self::FILE_NAME_CANCELLABLE),
            $this->document(self::FILE_NAME_CANCELLABLE)
        );

        $this->assertNotEmpty($details->data->id);

        return $details->data->id;
    }

    #[Depends('testCreateBatchForDeletion')]
    public function testDeleteBatch(string $batchId): void
    {
        // Give the batch time to reach a deletable state.
        $this->waitFor(10);

        $batches = $this->batches();
        $batches->delete($batchId, true);

        $this->expectException(JsonApiException::class);

        $batches->getDetails($batchId);
    }

    private function batchCreateAttributes(string $fileName): BatchCreateAttributes
    {
        return (new BatchCreateAttributes())
            ->setName('Integration Test Batch')
            ->setIcon('document')
            ->setChannelType('post')
            ->setFileOriginalName($fileName)
            ->setAddressPosition('left')
            ->setGroupingType('merge')
            ->setGroupingOptionsSplitType('qr_invoice')
            ->setGroupingOptionsSplitSize(2);
    }
}
