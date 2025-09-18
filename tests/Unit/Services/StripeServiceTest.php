<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\StripeService;
use PHPUnit\Framework\TestCase;
use Stripe\Exception\ApiErrorException;

class StripeServiceTest extends TestCase
{
    public function testCreateCheckoutSessionReturnsIdentifiers(): void
    {
        $config = [
            'secret_key' => 'sk_test_123',
            'payment_methods' => ['card'],
            'currency' => 'eur',
            'payment_description' => 'Test Payment',
            'success_url' => 'https://example.test/success',
            'cancel_url' => 'https://example.test/cancel',
            'webhook_secret' => 'whsec_test',
        ];

        $capturedParams = null;
        $capturedApiKey = null;

        $service = new StripeService(
            $config,
            function (array $params) use (&$capturedParams) {
                $capturedParams = $params;

                return (object) [
                    'id' => 'cs_test_123',
                    'url' => 'https://stripe.test/checkout'
                ];
            },
            null,
            function (string $apiKey) use (&$capturedApiKey): void {
                $capturedApiKey = $apiKey;
            }
        );

        $result = $service->createCheckoutSession(12.5, 42, 'donor@example.test');

        self::assertSame('cs_test_123', $result['id']);
        self::assertSame('https://stripe.test/checkout', $result['url']);
        self::assertSame('sk_test_123', $capturedApiKey);
        self::assertNotNull($capturedParams);
        self::assertSame('eur', $capturedParams['line_items'][0]['price_data']['currency']);
        self::assertSame(1250, $capturedParams['line_items'][0]['price_data']['unit_amount']);
        self::assertSame('donor@example.test', $capturedParams['metadata']['donor_email']);
    }

    public function testHandleWebhookTransformsCheckoutCompletionEvent(): void
    {
        $config = [
            'secret_key' => 'sk_test_123',
            'payment_methods' => [],
            'currency' => 'eur',
            'payment_description' => 'Test Payment',
            'success_url' => '',
            'cancel_url' => '',
            'webhook_secret' => 'whsec_test',
        ];

        $service = new StripeService(
            $config,
            function (array $params): void {
            },
            function (string $payload, string $sigHeader, string $secret) {
                $session = (object) [
                    'metadata' => (object) [
                        'creator_id' => 77,
                        'donor_email' => 'donor@example.test',
                    ],
                    'amount_total' => 3050,
                    'payment_intent' => 'pi_test_123',
                ];

                return (object) [
                    'type' => 'checkout.session.completed',
                    'data' => (object) ['object' => $session],
                ];
            }
        );

        $payload = '{}';
        $sigHeader = 't=1,v1=signature';

        $result = $service->handleWebhook($payload, $sigHeader);

        self::assertSame('payment_success', $result['type']);
        self::assertSame(77, $result['creator_id']);
        self::assertSame('donor@example.test', $result['donor_email']);
        self::assertSame(30.5, $result['amount']);
        self::assertSame('pi_test_123', $result['payment_id']);
    }

    public function testCreateCheckoutSessionRethrowsStripeExceptions(): void
    {
        $this->expectExceptionMessage('Erreur lors de la création de la session de paiement');

        $service = new StripeService(
            [
                'secret_key' => 'sk_test_123',
                'payment_methods' => [],
                'currency' => 'eur',
                'payment_description' => 'Test Payment',
                'success_url' => '',
                'cancel_url' => '',
                'webhook_secret' => 'whsec_test',
            ],
            function (array $params): void {
                throw new class('Stripe error', 0) extends ApiErrorException {
                };
            }
        );

        $service->createCheckoutSession(10.0, 1, 'donor@example.test');
    }
}
