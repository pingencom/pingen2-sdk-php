<?php

declare(strict_types=1);

namespace Tests\Integration;

use League\OAuth2\Client\Token\AccessTokenInterface;
use Pingen\Endpoints\BatchesEndpoint;
use Pingen\Endpoints\BatchEventsEndpoint;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Ebill\EbillMetaDataAttributes;
use Pingen\Endpoints\DataTransferObjects\Deliveries\Email\EmailMetaDataAttributes;
use Pingen\Endpoints\EbillEventsEndpoint;
use Pingen\Endpoints\EbillsEndpoint;
use Pingen\Endpoints\EmailEventsEndpoint;
use Pingen\Endpoints\EmailsEndpoint;
use Pingen\Endpoints\LetterEventsEndpoint;
use Pingen\Endpoints\LettersEndpoint;
use Pingen\Endpoints\OrganisationsEndpoint;
use Pingen\Endpoints\UserAssociationsEndpoint;
use Pingen\Endpoints\UserEndpoint;
use Pingen\Endpoints\WebhooksEndpoint;
use Pingen\Provider\Pingen;
use Pingen\ResourceEndpoint;
use Tests\TestCase;

/**
 * Base class for the tests that talk to the real Pingen staging api.
 *
 * Credentials are read from a .env file in the repository root (copy
 * .env.example and fill it in) or from real environment variables, which take
 * precedence so that ci can inject secrets without writing a file.
 *
 * @package Tests\Integration
 */
abstract class IntegrationTestCase extends TestCase
{
    /**
     * Scopes requested for the staging token, covering every resource below.
     */
    public const SCOPE = 'letter batch webhook organisation_read email ebill';

    public const FILE_NAME = 'test.pdf';

    /**
     * Staging keeps deliveries created from this document in a state that can be
     * cancelled, which makes the cancel flow deterministic.
     */
    public const FILE_NAME_CANCELLABLE = 'test_simulate_cancellable.pdf';

    /**
     * @var array<string, string>|null
     */
    private static ?array $credentials = null;

    private static ?AccessTokenInterface $accessToken = null;

    private static ?string $organisationId = null;

    protected function setUp(): void
    {
        parent::setUp();

        $credentials = self::credentials();

        if ($credentials['PINGEN2_CLIENT_ID'] === '' || $credentials['PINGEN2_CLIENT_SECRET'] === '') {
            $this->markTestSkipped(
                'Integration credentials not configured. Copy .env.example to .env '
                . 'and fill in PINGEN2_CLIENT_ID / PINGEN2_CLIENT_SECRET.'
            );
        }
    }

    /**
     * @return array<string, string>
     */
    protected static function credentials(): array
    {
        if (self::$credentials !== null) {
            return self::$credentials;
        }

        $dotenv = self::parseDotenv(self::repositoryRoot() . '/.env');

        $credentials = [];

        foreach ([
            'PINGEN2_CLIENT_ID',
            'PINGEN2_CLIENT_SECRET',
            'PINGEN2_ORGANIZATION_ID',
            'PINGEN2_ORGANIZATION_NAME',
            'PINGEN2_USE_STAGING',
        ] as $key) {
            $fromEnvironment = getenv($key);

            $credentials[$key] = is_string($fromEnvironment) && $fromEnvironment !== ''
                ? $fromEnvironment
                : ($dotenv[$key] ?? '');
        }

        return self::$credentials = $credentials;
    }

    /**
     * Integration tests must never run against production.
     */
    protected static function useStaging(): bool
    {
        $raw = strtolower(trim(self::credentials()['PINGEN2_USE_STAGING'] ?: 'true'));

        return ! in_array($raw, ['0', 'false', 'no', 'off'], true);
    }

    /**
     * A single token is fetched once and reused by every test.
     */
    protected static function accessToken(): AccessTokenInterface
    {
        if (self::$accessToken === null) {
            self::$accessToken = self::newAccessToken();
        }

        return self::$accessToken;
    }

    protected static function newAccessToken(): AccessTokenInterface
    {
        $credentials = self::credentials();

        $provider = new Pingen([
            'clientId' => $credentials['PINGEN2_CLIENT_ID'],
            'clientSecret' => $credentials['PINGEN2_CLIENT_SECRET'],
            'staging' => self::useStaging(),
        ]);

        return $provider->getAccessToken('client_credentials', ['scope' => self::SCOPE]);
    }

    protected function organisationId(): string
    {
        if (self::$organisationId !== null) {
            return self::$organisationId;
        }

        $configured = self::credentials()['PINGEN2_ORGANIZATION_ID'];

        if ($configured !== '') {
            return self::$organisationId = $configured;
        }

        $collection = $this->organisations()->getCollection();

        $this->assertNotEmpty($collection->data, 'No organisations returned - check the staging credentials.');

        return self::$organisationId = $collection->data[0]->id;
    }

    protected function organisationName(): string
    {
        return self::credentials()['PINGEN2_ORGANIZATION_NAME'];
    }

    protected function organisations(): OrganisationsEndpoint
    {
        return $this->endpoint(OrganisationsEndpoint::class);
    }

    protected function users(): UserEndpoint
    {
        return $this->endpoint(UserEndpoint::class);
    }

    protected function userAssociations(): UserAssociationsEndpoint
    {
        return $this->endpoint(UserAssociationsEndpoint::class);
    }

    protected function letters(): LettersEndpoint
    {
        return $this->endpoint(LettersEndpoint::class)->setOrganisationId($this->organisationId());
    }

    protected function letterEvents(string $letterId): LetterEventsEndpoint
    {
        return $this->endpoint(LetterEventsEndpoint::class)
            ->setOrganisationId($this->organisationId())
            ->setLetterId($letterId);
    }

    protected function batches(): BatchesEndpoint
    {
        return $this->endpoint(BatchesEndpoint::class)->setOrganisationId($this->organisationId());
    }

    protected function batchEvents(string $batchId): BatchEventsEndpoint
    {
        return $this->endpoint(BatchEventsEndpoint::class)
            ->setOrganisationId($this->organisationId())
            ->setBatchId($batchId);
    }

    protected function webhooks(): WebhooksEndpoint
    {
        return $this->endpoint(WebhooksEndpoint::class)->setOrganisationId($this->organisationId());
    }

    protected function emails(): EmailsEndpoint
    {
        return $this->endpoint(EmailsEndpoint::class)->setOrganisationId($this->organisationId());
    }

    protected function emailEvents(string $emailId): EmailEventsEndpoint
    {
        return $this->endpoint(EmailEventsEndpoint::class)
            ->setOrganisationId($this->organisationId())
            ->setEmailId($emailId);
    }

    protected function ebills(): EbillsEndpoint
    {
        return $this->endpoint(EbillsEndpoint::class)->setOrganisationId($this->organisationId());
    }

    protected function ebillEvents(string $ebillId): EbillEventsEndpoint
    {
        return $this->endpoint(EbillEventsEndpoint::class)
            ->setOrganisationId($this->organisationId())
            ->setEbillId($ebillId);
    }

    /**
     * @template T of ResourceEndpoint
     * @param class-string<T> $endpoint
     * @return T
     */
    protected function endpoint(string $endpoint): ResourceEndpoint
    {
        $instance = new $endpoint(self::accessToken());

        if (self::useStaging()) {
            $instance->useStaging();
        }

        return $instance;
    }

    protected function documentPath(string $fileName = self::FILE_NAME): string
    {
        return __DIR__ . '/files/' . $fileName;
    }

    /**
     * @param string $fileName
     * @return resource
     */
    protected function document(string $fileName = self::FILE_NAME)
    {
        $file = fopen($this->documentPath($fileName), 'r');

        $this->assertIsResource($file, sprintf('Cannot open %s', $this->documentPath($fileName)));

        return $file;
    }

    protected function emailMetaData(): EmailMetaDataAttributes
    {
        return (new EmailMetaDataAttributes())
            ->setSenderName('Pingen Test')
            ->setRecipientEmail('grzegorz.morgas@pingen.com')
            ->setRecipientName('Test Recipient')
            ->setReplyEmail('noreply@example.com')
            ->setReplyName('Reply Test')
            ->setSubject('Integration Test Email')
            ->setContent("Dear Recipient\n\nThis is an integration test.\n\nBest regards");
    }

    /**
     * Unique per call so that repeated runs never clash on a duplicate invoice number.
     */
    protected function ebillMetaData(): EbillMetaDataAttributes
    {
        return (new EbillMetaDataAttributes())
            ->setInvoiceNumber('INV-' . substr(bin2hex(random_bytes(6)), 0, 12))
            ->setInvoiceDate(date('Y-m-d'))
            ->setInvoiceDueDate(date('Y-m-d', strtotime('+30 days')))
            ->setRecipientIdentifier('41100000014283293');
    }

    /**
     * Waits for the api to move a delivery into the state the next step needs.
     */
    protected function waitFor(int $seconds): void
    {
        sleep($seconds);
    }

    private static function repositoryRoot(): string
    {
        return dirname(__DIR__, 2);
    }

    /**
     * @param string $path
     * @return array<string, string>
     */
    private static function parseDotenv(string $path): array
    {
        if (! is_file($path)) {
            return [];
        }

        $values = [];

        foreach ((array) file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $rawLine) {
            $line = trim((string) $rawLine);

            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $values[trim($key)] = trim(trim(trim($value), '"'), "'");
        }

        return $values;
    }
}
