<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterPriceCalculationAttributes;
use Pingen\Endpoints\ParameterBags\LetterCollectionParameterBag;
use Pingen\Exceptions\JsonApiException;

#[Group('integration')]
class LettersIntegrationTest extends IntegrationTestCase
{
    public function testListLetters(): void
    {
        $collection = $this->letters()->getCollection();

        $this->assertGreaterThanOrEqual(0, count($collection->data));
    }

    public function testListLettersPaginated(): void
    {
        $collection = $this->letters()->getCollection(
            (new LetterCollectionParameterBag())
                ->setPageNumber(1)
                ->setPageLimit(3)
        );

        $this->assertLessThanOrEqual(3, count($collection->data));
    }

    public function testCreateLetter(): string
    {
        $letters = $this->letters();

        $details = $letters->uploadAndCreate(
            (new LetterCreateAttributes())
                ->setFileOriginalName(self::FILE_NAME)
                ->setAddressPosition('left')
                ->setAutoSend(true)
                ->setDeliveryProduct('cheap')
                ->setPrintMode('simplex')
                ->setPrintSpectrum('grayscale'),
            $this->document()
        );

        $this->assertNotEmpty($details->data->id);
        $this->assertSame('validating', $details->data->attributes->status);

        $this->assertSame($details->data->id, $letters->getDetails($details->data->id)->data->id);

        // Give the api time to make the new letter show up in the collection.
        $this->waitFor(30);

        $collection = $letters->getCollection(
            (new LetterCollectionParameterBag())
                ->setSort('-created_at')
                ->setPageNumber(1)
                ->setPageLimit(20)
        );

        $ids = array_map(fn ($item): string => $item->id, $collection->data);

        $this->assertContains($details->data->id, $ids, 'Newest first, so the new letter must be on the first page.');

        return $details->data->id;
    }

    #[Depends('testCreateLetter')]
    public function testGetLetterById(string $letterId): void
    {
        $details = $this->letters()->getDetails($letterId);

        $this->assertSame($letterId, $details->data->id);
        $this->assertNotEmpty($details->data->attributes->status);
    }

    #[Depends('testCreateLetter')]
    public function testGetLetterEvents(string $letterId): void
    {
        $collection = $this->letterEvents($letterId)->getCollection();

        $this->assertGreaterThanOrEqual(0, count($collection->data));
    }

    #[Depends('testCreateLetter')]
    public function testGetLetterFile(string $letterId): void
    {
        $file = $this->letters()->getFile($letterId);

        $this->assertIsResource($file);
        $this->assertNotEmpty(stream_get_contents($file));

        fclose($file);
    }

    #[Depends('testCreateLetter')]
    public function testGetLetterSentEvents(string $letterId): void
    {
        $this->assertGreaterThanOrEqual(0, count($this->letterEvents($letterId)->getSentCollection()->data));
    }

    #[Depends('testCreateLetter')]
    public function testGetLetterDeliveredEvents(string $letterId): void
    {
        $this->assertGreaterThanOrEqual(0, count($this->letterEvents($letterId)->getDeliveredCollection()->data));
    }

    #[Depends('testCreateLetter')]
    public function testGetLetterIssueEvents(string $letterId): void
    {
        $this->assertGreaterThanOrEqual(0, count($this->letterEvents($letterId)->getIssuesCollection()->data));
    }

    #[Depends('testCreateLetter')]
    public function testGetLetterUndeliverableEvents(string $letterId): void
    {
        $this->assertGreaterThanOrEqual(0, count($this->letterEvents($letterId)->getUndeliverableCollection()->data));
    }

    public function testCalculateLetterPrice(): void
    {
        $price = $this->letters()->calculatePrice(
            (new LetterPriceCalculationAttributes())
                ->setCountry('CH')
                ->setPaperTypes(['normal', 'normal'])
                ->setPrintMode('simplex')
                ->setPrintSpectrum('grayscale')
                ->setDeliveryProduct('cheap')
        );

        $this->assertNotEmpty($price->data->id);
    }

    public function testCreateCancellableLetter(): string
    {
        $details = $this->letters()->uploadAndCreate(
            (new LetterCreateAttributes())
                ->setFileOriginalName(self::FILE_NAME_CANCELLABLE)
                ->setAddressPosition('left')
                ->setAutoSend(true)
                ->setDeliveryProduct('cheap')
                ->setPrintMode('simplex')
                ->setPrintSpectrum('grayscale'),
            $this->document(self::FILE_NAME_CANCELLABLE)
        );

        $this->assertNotEmpty($details->data->id);
        $this->assertSame('validating', $details->data->attributes->status);

        return $details->data->id;
    }

    #[Depends('testCreateCancellableLetter')]
    public function testCancelLetter(string $letterId): void
    {
        // Give the letter time to reach a cancellable state.
        $this->waitFor(10);

        $letters = $this->letters();
        $letters->cancel($letterId);

        $this->assertNotEmpty($letters->getDetails($letterId)->data->attributes->status);
    }

    public function testCreateLetterForDeletion(): string
    {
        $details = $this->letters()->uploadAndCreate(
            (new LetterCreateAttributes())
                ->setFileOriginalName(self::FILE_NAME)
                ->setAddressPosition('right')
                ->setAutoSend(false),
            $this->document()
        );

        $this->assertNotEmpty($details->data->id);
        $this->assertSame('validating', $details->data->attributes->status);

        return $details->data->id;
    }

    #[Depends('testCreateLetterForDeletion')]
    public function testDeleteLetter(string $letterId): void
    {
        $this->waitFor(5);

        $letters = $this->letters();
        $letters->delete($letterId);

        $this->expectException(JsonApiException::class);

        $letters->getDetails($letterId);
    }
}
