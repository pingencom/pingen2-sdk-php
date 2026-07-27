<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailDetails;
use Pingen\Exceptions\JsonApiException;

#[Group('integration')]
class EmailsIntegrationTest extends IntegrationTestCase
{
    public function testListEmails(): void
    {
        $collection = $this->emails()->getCollection();

        $this->assertGreaterThanOrEqual(0, count($collection->data));
    }

    public function testCreateEmail(): string
    {
        $details = $this->createEmail(self::FILE_NAME, true);

        $this->assertNotEmpty($details->data->id);
        $this->assertNotEmpty($details->data->attributes->status);

        return $details->data->id;
    }

    #[Depends('testCreateEmail')]
    public function testGetEmailById(string $emailId): void
    {
        $details = $this->emails()->getDetails($emailId);

        $this->assertSame($emailId, $details->data->id);
        $this->assertNotEmpty($details->data->attributes->status);
    }

    #[Depends('testCreateEmail')]
    public function testGetEmailEvents(string $emailId): void
    {
        $collection = $this->emailEvents($emailId)->getCollection();

        $this->assertGreaterThanOrEqual(0, count($collection->data));
    }

    #[Depends('testCreateEmail')]
    public function testGetEmailFile(string $emailId): void
    {
        // Give the email time to reach a retrievable state.
        $this->waitFor(5);

        $file = $this->emails()->getFile($emailId);

        $this->assertIsResource($file);
        $this->assertNotEmpty(stream_get_contents($file));

        fclose($file);
    }

    public function testCreateCancellableEmail(): string
    {
        $details = $this->createEmail(self::FILE_NAME_CANCELLABLE, true);

        $this->assertNotEmpty($details->data->id);

        return $details->data->id;
    }

    #[Depends('testCreateCancellableEmail')]
    public function testCancelEmail(string $emailId): void
    {
        // Give the email time to reach a cancellable state.
        $this->waitFor(10);

        $emails = $this->emails();
        $emails->cancel($emailId);

        $this->assertNotEmpty($emails->getDetails($emailId)->data->attributes->status);
    }

    public function testCreateEmailForDeletion(): string
    {
        $details = $this->createEmail(self::FILE_NAME, false);

        $this->assertNotEmpty($details->data->id);

        return $details->data->id;
    }

    #[Depends('testCreateEmailForDeletion')]
    public function testDeleteEmail(string $emailId): void
    {
        // Give the email time to reach a deletable state.
        $this->waitFor(10);

        $emails = $this->emails();
        $emails->delete($emailId);

        $this->expectException(JsonApiException::class);

        $emails->getDetails($emailId);
    }

    private function createEmail(string $fileName, bool $autoSend): EmailDetails
    {
        return $this->emails()->uploadAndCreate(
            (new EmailCreateAttributes())
                ->setFileOriginalName($fileName)
                ->setAutoSend($autoSend)
                ->setMetaData($this->emailMetaData()),
            $this->document($fileName)
        );
    }
}
