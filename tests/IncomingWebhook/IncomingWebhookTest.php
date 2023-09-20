<?php

declare(strict_types=1);

namespace Tests\IncomingWebhook;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Pingen\Exceptions\WebhookSignatureException;
use Pingen\IncomingWebhook\DataTransferObjects\IncomingWebhookDetails;
use Pingen\IncomingWebhook\IncomingWebhook;
use Tests\TestCase;

class IncomingWebhookTest extends TestCase
{
    protected string $secret;
    protected IncomingWebhook $incomingWebhook;

    public function setUp(): void
    {
        parent::setUp();

        $this->secret = 'webhook_test_secret';
        $this->incomingWebhook = new IncomingWebhook($this->secret);
    }

    public function testPositive(): void
    {
        $request = Request::create(
            '/webhook/incoming/test',
            Request::METHOD_POST,
            [],
            [],
            [],
            [],
            '{"data":{"type":"webhook_issues","id":"a3233e48-5e70-4138-95b2-a72d4875016b","attributes":{"reason":"Page limit exceeded","url":"https:\/\/test\/receiver","created_at":"2023-08-03T11:24:39+0200"},"relationships":{"organisation":{"links":{"related":"http:\/\/api-test.v2.pingen.com\/organisations\/2017973a-6403-444d-af05-eb4b2b7f5e2f"},"data":{"type":"organisations","id":"2017973a-6403-444d-af05-eb4b2b7f5e2f"}},"letter":{"links":{"related":"http:\/\/api-test.v2.pingen.com\/organisations\/2017973a-6403-444d-af05-eb4b2b7f5e2f\/letters\/4f31cdb2-bc0d-4db5-a13d-3336958dba02"},"data":{"type":"letters","id":"4f31cdb2-bc0d-4db5-a13d-3336958dba02"}},"event":{"data":{"type":"letters_events","id":"ba08eb5f-413c-4dd1-8ed6-aac2b96124d0"}}}},"included":[{"type":"organisations","id":"2017973a-6403-444d-af05-eb4b2b7f5e2f","attributes":{"name":"Prof. Leopoldo Hahn","status":"active","plan":"free","billing_mode":"postpaid","billing_currency":"CHF","billing_balance":0,"default_country":"CH","edition":"pingen","default_address_position":"left","data_retention_addresses":12,"data_retention_pdf":12,"color":"#0758FF","created_at":"2023-08-03T11:24:39+0200","updated_at":"2023-08-03T11:24:39+0200"},"links":{"self":"http:\/\/api-test.v2.pingen.com\/organisations\/2017973a-6403-444d-af05-eb4b2b7f5e2f"}},{"type":"letters","id":"4f31cdb2-bc0d-4db5-a13d-3336958dba02","attributes":{"status":"validating","file_original_name":"ullam.pdf","file_pages":null,"address":null,"address_position":"left","country":null,"delivery_product":"fast","print_mode":"simplex","print_spectrum":"color","price_currency":null,"price_value":null,"paper_types":null,"fonts":null,"source":"app","tracking_number":null,"submitted_at":null,"created_at":"2023-08-03T11:24:39+0200","updated_at":"2023-08-03T11:24:39+0200"},"links":{"self":"http:\/\/api-test.v2.pingen.com\/organisations\/2017973a-6403-444d-af05-eb4b2b7f5e2f\/letters\/4f31cdb2-bc0d-4db5-a13d-3336958dba02"}},{"type":"letters_events","id":"ba08eb5f-413c-4dd1-8ed6-aac2b96124d0","attributes":{"code":"file_too_many_pages","name":"Page limit exceeded","producer":"Pingen","location":"","has_image":false,"data":[],"emitted_at":"2023-08-03T11:24:39+0200","created_at":"2023-08-03T11:24:39+0200","updated_at":"2023-08-03T11:24:39+0200"}}]}'
        );
        $request->headers->set('Signature', '812ac7c9776458ce47f1796faec3eca4b15f47b3b9bcfeca3ad90cc190ba0c27');

        $response = $this->incomingWebhook->processWebhook($request);

        $this->assertInstanceOf(IncomingWebhookDetails::class, $response);
    }

    public function testRequestAreNotPostMethod(): void
    {
        $this->expectException(WebhookSignatureException::class);
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('Only POST requests are allowed.');

        $request = Request::create(
            '/webhook/incoming/test',
            Request::METHOD_GET
        );

        $this->incomingWebhook->processWebhook($request);
    }

    public function testSignatureHeaderMissing(): void
    {
        $this->expectException(WebhookSignatureException::class);
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('Signature missing.');

        $request = Request::create(
            '/webhook/incoming/test',
            Request::METHOD_POST
        );

        $this->incomingWebhook->processWebhook($request);
    }

    public function testSignatureAreDifferent(): void
    {
        $this->expectException(WebhookSignatureException::class);
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('Webhook signature matching failed.');

        $request = Request::create(
            '/webhook/incoming/test',
            Request::METHOD_POST,
        );
        $request->headers->set('Signature', 'example');

        $this->incomingWebhook->processWebhook($request);
    }
}