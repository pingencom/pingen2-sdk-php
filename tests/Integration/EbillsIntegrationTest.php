<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill\EbillCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill\EbillDetails;
use Pingen\Exceptions\JsonApiException;

#[Group('integration')]
class EbillsIntegrationTest extends IntegrationTestCase
{
    public function testListEbills(): void
    {
        $collection = $this->ebills()->getCollection();

        $this->assertGreaterThanOrEqual(0, count($collection->data));
    }

    public function testCreateEbill(): string
    {
        $details = $this->createEbill(self::FILE_NAME, false);

        $this->assertNotEmpty($details->data->id);
        $this->assertNotEmpty($details->data->attributes->status);

        return $details->data->id;
    }

    #[Depends('testCreateEbill')]
    public function testGetEbillById(string $ebillId): void
    {
        $details = $this->ebills()->getDetails($ebillId);

        $this->assertSame($ebillId, $details->data->id);
        $this->assertNotEmpty($details->data->attributes->status);
    }

    #[Depends('testCreateEbill')]
    public function testGetEbillEvents(string $ebillId): void
    {
        $collection = $this->ebillEvents($ebillId)->getCollection();

        $this->assertGreaterThanOrEqual(0, count($collection->data));
    }

    #[Depends('testCreateEbill')]
    public function testGetEbillFile(string $ebillId): void
    {
        // Give the e-bill time to reach a retrievable state.
        $this->waitFor(10);

        $file = $this->ebills()->getFile($ebillId);

        $this->assertIsResource($file);
        $this->assertNotEmpty(stream_get_contents($file));

        fclose($file);
    }

    #[Depends('testCreateEbill')]
    public function testSendEbill(string $ebillId): void
    {
        // An e-bill can only be sent once it left the validating state.
        $this->waitFor(10);

        $details = $this->ebills()->send($ebillId);

        $this->assertSame($ebillId, $details->data->id);
    }

    public function testCreateCancellableEbill(): string
    {
        $details = $this->createEbill(self::FILE_NAME_CANCELLABLE, true);

        $this->assertNotEmpty($details->data->id);

        return $details->data->id;
    }

    #[Depends('testCreateCancellableEbill')]
    public function testCancelEbill(string $ebillId): void
    {
        // Give the e-bill time to reach a cancellable state.
        $this->waitFor(10);

        $ebills = $this->ebills();
        $ebills->cancel($ebillId);

        $this->assertNotEmpty($ebills->getDetails($ebillId)->data->attributes->status);
    }

    public function testCreateEbillForDeletion(): string
    {
        $details = $this->createEbill(self::FILE_NAME, false);

        $this->assertNotEmpty($details->data->id);

        return $details->data->id;
    }

    #[Depends('testCreateEbillForDeletion')]
    public function testDeleteEbill(string $ebillId): void
    {
        // Give the e-bill time to reach a deletable state.
        $this->waitFor(10);

        $ebills = $this->ebills();
        $ebills->delete($ebillId);

        $this->expectException(JsonApiException::class);

        $ebills->getDetails($ebillId);
    }

    /**
     * Creates an e-bill, skipping the test when the channel is not configured
     * for the organisation under test.
     */
    private function createEbill(string $fileName, bool $autoSend): EbillDetails
    {
        try {
            return $this->ebills()->uploadAndCreate(
                (new EbillCreateAttributes())
                    ->setFileOriginalName($fileName)
                    ->setAutoSend($autoSend)
                    ->setMetaData($this->ebillMetaData()),
                $this->document($fileName)
            );
        } catch (JsonApiException $jsonApiException) {
            if (str_contains((string) $jsonApiException->response->body(), 'conflict_missing_configuration')) {
                $this->markTestSkipped('E-Bill channel not configured - skipping.');
            }

            throw $jsonApiException;
        }
    }
}
