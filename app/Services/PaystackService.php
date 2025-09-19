<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaystackService
{
    protected string $baseUrl = 'https://api.paystack.co';

    protected function secret(): string
    {
        return (string) config('services.paystack.secret', env('PAYSTACK_SECRET'));
    }

    protected function currency(): string
    {
        return (string) (config('services.paystack.currency', env('PAYSTACK_CURRENCY', 'NGN')) ?: 'NGN');
    }

    public function initialize(int $amountInKobo, string $email, string $callbackUrl, ?string $reference = null): array
    {
        $ref = $reference ?: ('WALLET_' . Str::upper(Str::random(10)) . '_' . time());

        $payload = [
            'email' => $email,
            'amount' => $amountInKobo,
            'currency' => $this->currency(),
            'reference' => $ref,
            'callback_url' => $callbackUrl,
        ];

        $response = Http::withToken($this->secret())
            ->acceptJson()
            ->post($this->baseUrl . '/transaction/initialize', $payload);

        if (!$response->ok()) {
            return [
                'status' => false,
                'message' => $response->json('message') ?? 'Unable to initialize payment',
            ];
        }

        $data = $response->json('data') ?? [];
        return [
            'status' => (bool) ($response->json('status') ?? false),
            'authorization_url' => $data['authorization_url'] ?? null,
            'reference' => $data['reference'] ?? $ref,
            'access_code' => $data['access_code'] ?? null,
            'raw' => $response->json(),
        ];
    }

    public function verify(string $reference): array
    {
        $response = Http::withToken($this->secret())
            ->acceptJson()
            ->get($this->baseUrl . '/transaction/verify/' . urlencode($reference));

        if (!$response->ok()) {
            return [
                'status' => false,
                'message' => $response->json('message') ?? 'Verification failed',
                'raw' => $response->json(),
            ];
        }

        $body = $response->json();
        $data = $body['data'] ?? [];
        $success = ($body['status'] ?? false) && (($data['status'] ?? '') === 'success');

        return [
            'status' => $success,
            'amount_kobo' => (int) ($data['amount'] ?? 0),
            'currency' => (string) ($data['currency'] ?? ''),
            'customer_email' => (string) (($data['customer']['email'] ?? '') ?: ''),
            'reference' => (string) ($data['reference'] ?? $reference),
            'gateway_response' => (string) ($data['gateway_response'] ?? ''),
            'paid_at' => (string) ($data['paid_at'] ?? ''),
            'raw' => $body,
        ];
    }
}
