<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PayPalService
{
    private const BASE_URL = 'https://api-m.sandbox.paypal.com';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {}

    /**
     * Creates a PayPal order and returns its ID.
     */
    public function createOrder(float $amount, string $currency = 'EUR'): string
    {
        $token = $this->getAccessToken();

        $response = $this->httpClient->request('POST', self::BASE_URL . '/v2/checkout/orders', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
                'PayPal-Request-Id' => uniqid('order_', true),
            ],
            'json' => [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'brand_name'          => "La Nîmes'Alerie",
                    'landing_page'        => 'NO_PREFERENCE',
                    'user_action'         => 'PAY_NOW',
                    'shipping_preference' => 'NO_SHIPPING',
                ],
            ],
        ]);

        return $response->toArray()['id'];
    }

    /**
     * Captures an approved PayPal order and returns the full capture response.
     */
    public function captureOrder(string $paypalOrderId): array
    {
        $token = $this->getAccessToken();

        $response = $this->httpClient->request(
            'POST',
            self::BASE_URL . '/v2/checkout/orders/' . $paypalOrderId . '/capture',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [],
            ]
        );

        return $response->toArray();
    }

    /**
     * Fetches an OAuth2 access token from PayPal using client credentials.
     */
    private function getAccessToken(): string
    {
        $response = $this->httpClient->request('POST', self::BASE_URL . '/v1/oauth2/token', [
            'auth_basic' => [$this->clientId, $this->clientSecret],
            'headers'    => ['Accept' => 'application/json'],
            'body'       => 'grant_type=client_credentials',
        ]);

        return $response->toArray()['access_token'];
    }
}
