<?php

namespace App\Services;

use App\Mail\MarketplaceNotificationMail;
use App\Models\ContractMilestone;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DodoPaymentService
{
    protected ?string $apiKey;
    protected string $environment;
    protected ?string $webhookKey;

    public function __construct()
    {
        $this->apiKey = config('services.dodo.api_key');
        $this->environment = config('services.dodo.environment', 'test_mode');
        $this->webhookKey = config('services.dodo.webhook_key');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    protected function getBaseUrl(): string
    {
        return $this->environment === 'live_mode'
            ? 'https://live.dodopayments.com'
            : 'https://test.dodopayments.com';
    }

    /**
     * Get or dynamically create a marketplace product in Dodo Payments
     */
    public function getOrCreateProductId(): ?string
    {
        return Cache::remember('dodo_marketplace_product_id', 86400, function () {
            try {
                // 1. Check existing products
                $res = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])->get($this->getBaseUrl() . '/products');

                if ($res->successful()) {
                    $items = $res->json()['items'] ?? [];
                    if (!empty($items) && isset($items[0]['product_id'])) {
                        return $items[0]['product_id'];
                    }
                }

                // 2. Create default deposit product
                $createRes = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->post($this->getBaseUrl() . '/products', [
                    'name' => 'Marketplace Escrow & Wallet Deposit',
                    'description' => 'Freelance marketplace escrow balance top-up',
                    'tax_category' => 'digital_products',
                    'price' => [
                        'type' => 'one_time_price',
                        'currency' => 'USD',
                        'price' => 1000,
                        'discount' => 0,
                        'purchasing_power_parity' => false,
                        'pay_what_you_want' => true,
                    ],
                ]);

                if ($createRes->successful() && isset($createRes->json()['product_id'])) {
                    return $createRes->json()['product_id'];
                }
            } catch (\Throwable $e) {
                Log::error('Dodo Product Init Error: ' . $e->getMessage());
            }

            return 'pdt_0NlNOZyikONq2Byv7JsaM';
        });
    }

    /**
     * Create official Dodo Payments Checkout Session
     */
    public function createCheckoutSession(User $user, float $amount, string $purpose = 'wallet_deposit', ?int $referenceId = null): array
    {
        $returnUrl = route('payments.dodo.return', [
            'amount' => $amount,
            'purpose' => $purpose,
            'reference_id' => $referenceId,
            'status' => 'success',
        ]);

        if ($this->isConfigured()) {
            try {
                $productId = $this->getOrCreateProductId() ?? 'pdt_0NlNOZyikONq2Byv7JsaM';
                $amountCents = (int) round($amount * 100);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->post($this->getBaseUrl() . '/payments', [
                    'billing' => [
                        'city' => $user->city ?? 'Austin',
                        'country' => 'US',
                        'state' => 'TX',
                        'street' => '100 Innovation Way',
                        'zipcode' => '78701',
                    ],
                    'customer' => [
                        'email' => $user->email,
                        'name' => $user->name,
                    ],
                    'payment_link' => true,
                    'product_cart' => [
                        [
                            'product_id' => $productId,
                            'quantity' => 1,
                            'amount' => $amountCents,
                        ]
                    ],
                    'return_url' => $returnUrl,
                    'metadata' => [
                        'user_id' => (string) $user->id,
                        'purpose' => $purpose,
                        'reference_id' => (string) ($referenceId ?? 0),
                    ],
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $link = $data['payment_link'] ?? ($data['checkout_url'] ?? null);

                    if ($link) {
                        return [
                            'success' => true,
                            'checkout_url' => $link,
                            'payment_id' => $data['payment_id'] ?? null,
                            'is_simulation' => false,
                        ];
                    }
                } else {
                    Log::error('Dodo Payments API Error response: ' . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error('Dodo Exception: ' . $e->getMessage());
            }
        }

        // Fallback simulator only if API fails or offline
        return [
            'success' => true,
            'checkout_url' => route('payments.dodo.simulator', [
                'amount' => $amount,
                'purpose' => $purpose,
                'reference_id' => $referenceId,
                'user_id' => $user->id,
            ]),
            'is_simulation' => true,
        ];
    }

    /**
     * Process verified payment fulfillment
     */
    public function fulfillPayment(User $user, float $amount, string $purpose = 'wallet_deposit', ?int $referenceId = null): array
    {
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

        if ($purpose === 'milestone_escrow' && $referenceId) {
            $milestone = ContractMilestone::with('contract')->find($referenceId);
            if ($milestone && $milestone->status === 'pending') {
                $wallet->escrow_balance += $amount;
                $wallet->save();

                $milestone->update([
                    'status' => 'funded_in_escrow',
                    'funded_at' => now(),
                ]);

                Transaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $user->id,
                    'type' => 'escrow_lock',
                    'amount' => $amount,
                    'fee' => 0,
                    'reference_type' => 'ContractMilestone',
                    'reference_id' => $milestone->id,
                    'description' => "Dodo Payments: Escrow funded for Milestone #{$milestone->id} ({$milestone->title})",
                    'status' => 'completed',
                ]);

                // Send Email to Freelancer
                try {
                    $freelancer = $milestone->contract->freelancer;
                    if ($freelancer && $freelancer->email) {
                        Mail::to($freelancer->email)->send(
                            new MarketplaceNotificationMail(
                                subject: '🔒 Milestone Funded in Escrow ($' . number_format($amount, 2) . ')',
                                greeting: 'Hi ' . $freelancer->name . ',',
                                mainMessage: $user->name . ' has funded Milestone "' . $milestone->title . '" with $' . number_format($amount, 2) . ' via Dodo Payments. You are protected and can begin work.',
                                actionUrl: route('contracts.show', $milestone->contract_id),
                                actionText: 'View Milestone & Start Work',
                                details: [
                                    'Contract' => $milestone->contract->title,
                                    'Funded Amount' => '$' . number_format($amount, 2),
                                    'Payment Method' => 'Dodo Payments',
                                    'Status' => 'Protected in Escrow',
                                ]
                            )
                        );
                    }
                } catch (\Throwable $e) {
                    Log::warning('Escrow email failed: ' . $e->getMessage());
                }

                return ['success' => true, 'redirect' => route('contracts.show', $milestone->contract_id)];
            }
        }

        // Standard Wallet Deposit
        $wallet->balance += $amount;
        $wallet->save();

        Transaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $amount,
            'fee' => 0.00,
            'description' => 'Dodo Payments: Card / Apple Pay / UPI Deposit',
            'status' => 'completed',
        ]);

        // Send Deposit Receipt Email to User
        try {
            if ($user->email) {
                Mail::to($user->email)->send(
                    new MarketplaceNotificationMail(
                        subject: '💳 Payment Receipt: $' . number_format($amount, 2) . ' Deposited to Wallet',
                        greeting: 'Hi ' . $user->name . ',',
                        mainMessage: 'Your deposit of $' . number_format($amount, 2) . ' via Dodo Payments has been processed successfully and credited to your available wallet balance.',
                        actionUrl: route('wallet.index'),
                        actionText: 'View Wallet Balance',
                        details: [
                            'Deposit Amount' => '$' . number_format($amount, 2),
                            'Payment Gateway' => 'Dodo Payments (MoR)',
                            'New Available Balance' => '$' . number_format($wallet->balance, 2),
                            'Transaction Status' => 'Completed',
                        ]
                    )
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Deposit email failed: ' . $e->getMessage());
        }

        return ['success' => true, 'redirect' => route('wallet.index')];
    }
}
