<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use Pingen\Endpoints\DataTransferObjects\Webhook\WebhookCreateAttributes;
use Pingen\Exceptions\JsonApiException;

#[Group('integration')]
class WebhooksIntegrationTest extends IntegrationTestCase
{
    public const URL = 'https://httpbin.org/post';

    public function testListWebhooks(): void
    {
        $collection = $this->webhooks()->getCollection();

        $this->assertGreaterThanOrEqual(0, count($collection->data));
    }

    public function testCreateWebhook(): string
    {
        $details = $this->webhooks()->create(
            (new WebhookCreateAttributes())
                ->setEventCategory('issues')
                ->setUrl(self::URL)
                ->setSigningKey('integration-test-signing-key-32c')
        );

        $this->assertNotEmpty($details->data->id);
        $this->assertSame(self::URL, $details->data->attributes->url);

        return $details->data->id;
    }

    #[Depends('testCreateWebhook')]
    public function testGetWebhookById(string $webhookId): void
    {
        $details = $this->webhooks()->getDetails($webhookId);

        $this->assertSame($webhookId, $details->data->id);
        $this->assertSame(self::URL, $details->data->attributes->url);
    }

    #[Depends('testCreateWebhook')]
    public function testDeleteWebhook(string $webhookId): void
    {
        $webhooks = $this->webhooks();
        $webhooks->delete($webhookId);

        $this->expectException(JsonApiException::class);

        $webhooks->getDetails($webhookId);
    }
}
