<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Webhook;

class StripeService {
    private $config;
    /** @var callable */
    private $sessionFactory;
    /** @var callable */
    private $webhookConstructor;
    /** @var callable */
    private $apiKeySetter;

    public function __construct(?array $config = null, ?callable $sessionFactory = null, ?callable $webhookConstructor = null, ?callable $apiKeySetter = null) {
        $this->config = $config ?? require __DIR__ . '/../config/stripe.php';
        $this->sessionFactory = $sessionFactory ?? [Session::class, 'create'];
        $this->webhookConstructor = $webhookConstructor ?? [Webhook::class, 'constructEvent'];
        $this->apiKeySetter = $apiKeySetter ?? [Stripe::class, 'setApiKey'];

        call_user_func($this->apiKeySetter, $this->config['secret_key']);
    }

    /**
     * Crée une session de paiement Stripe Checkout
     *
     * @param float $amount Montant en euros
     * @param int $creatorId ID de la créatrice
     * @param string $donorEmail Email du donateur
     * @return array Session ID et URL
     * @throws \Exception
     */
    public function createCheckoutSession(float $amount, int $creatorId, string $donorEmail): array {
        try {
            $session = call_user_func($this->sessionFactory, [
                'payment_method_types' => $this->config['payment_methods'],
                'customer_email' => $donorEmail,
                'line_items' => [[
                    'price_data' => [
                        'currency' => $this->config['currency'],
                        'product_data' => [
                            'name' => $this->config['payment_description'],
                            'metadata' => [
                                'creator_id' => $creatorId
                            ]
                        ],
                        'unit_amount' => (int) ($amount * 100)
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->config['success_url'] . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->config['cancel_url'],
                'metadata' => [
                    'creator_id' => $creatorId,
                    'donor_email' => $donorEmail
                ]
            ]);

            return [
                'id' => $session->id,
                'url' => $session->url
            ];
        } catch (ApiErrorException $e) {
            error_log('Erreur Stripe: ' . $e->getMessage());
            throw new \Exception('Erreur lors de la création de la session de paiement');
        }
    }

    /**
     * Vérifie et traite un webhook Stripe
     *
     * @param string $payload Le corps brut de la requête
     * @param string $sigHeader L'en-tête de signature Stripe
     * @return array Données de l'événement
     * @throws \Exception
     */
    public function handleWebhook(string $payload, string $sigHeader): array {
        try {
            $event = call_user_func(
                $this->webhookConstructor,
                $payload,
                $sigHeader,
                $this->config['webhook_secret']
            );

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    return [
                        'type' => 'payment_success',
                        'creator_id' => $session->metadata->creator_id,
                        'donor_email' => $session->metadata->donor_email,
                        'amount' => $session->amount_total / 100,
                        'payment_id' => $session->payment_intent
                    ];

                case 'payment_intent.payment_failed':
                    $intent = $event->data->object;
                    return [
                        'type' => 'payment_failed',
                        'payment_id' => $intent->id,
                        'error' => $intent->last_payment_error->message ?? 'Unknown error'
                    ];

                default:
                    return ['type' => 'ignored'];
            }
        } catch (\UnexpectedValueException $e) {
            error_log('Erreur de signature webhook: ' . $e->getMessage());
            throw new \Exception('Signature webhook invalide');
        } catch (\Exception $e) {
            error_log('Erreur webhook: ' . $e->getMessage());
            throw new \Exception('Erreur lors du traitement du webhook');
        }
    }
}
