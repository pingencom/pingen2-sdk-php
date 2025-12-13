<?php

declare(strict_types=1);

namespace Pingen\IncomingWebhook;

use Illuminate\Http\Request;
use Pingen\Exceptions\WebhookSignatureException;
use Pingen\IncomingWebhook\DataTransferObjects\IncomingWebhookDetails;

class IncomingWebhook
{
    protected string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param Request $request
     * @return IncomingWebhookDetails
     * @throws WebhookSignatureException
     */
    public function processWebhook(Request $request): IncomingWebhookDetails
    {
        $this->verify($request);

        return new IncomingWebhookDetails(json_decode((string) $request->getContent(), true));
    }

    /**
     * @param Request $request
     * @return void
     * @throws WebhookSignatureException
     */
    private function verify(Request $request): void
    {
        if (! $request->isMethod('post')) {
            throw new WebhookSignatureException('Only POST requests are allowed.');
        }

        if (! $request->hasHeader('Signature')) {
            throw new WebhookSignatureException('Signature missing.');
        }

        if (! hash_equals(
            hash_hmac('sha256', (string) $request->getContent(), $this->secret),
            (string) $request->header('Signature')
        )) {
            throw new WebhookSignatureException('Webhook signature matching failed.');
        }
    }
}
