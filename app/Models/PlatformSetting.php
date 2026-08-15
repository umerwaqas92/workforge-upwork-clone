<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Get a setting value with caching and fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'integer' => (int) $setting->value,
                'float', 'decimal' => (float) $setting->value,
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'json', 'array' => json_decode($setting->value, true) ?? [],
                default => $setting->value,
            };
        });
    }

    /**
     * Set/update a setting value and clear cache.
     */
    public static function set(string $key, mixed $value): void
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            $setting->update([
                'value' => is_array($value) ? json_encode($value) : (string) $value,
            ]);
        } else {
            self::create([
                'key' => $key,
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'label' => ucwords(str_replace('_', ' ', $key)),
            ]);
        }

        Cache::forget("setting_{$key}");
    }

    /**
     * Seed default marketplace settings if empty.
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            [
                'key' => 'platform_fee_percent',
                'value' => '10.0',
                'type' => 'float',
                'group' => 'monetization',
                'label' => 'Platform Take-Rate Fee (%)',
                'description' => 'Percentage deducted from milestone payments released to freelancers (e.g. 10% on $1,000 = $100 platform revenue).',
            ],
            [
                'key' => 'client_processing_fee_percent',
                'value' => '3.0',
                'type' => 'float',
                'group' => 'monetization',
                'label' => 'Client Deposit Surcharge (%)',
                'description' => 'Payment processing fee charged to clients during wallet deposits & card payments.',
            ],
            [
                'key' => 'connect_cost_usd',
                'value' => '0.15',
                'type' => 'float',
                'group' => 'monetization',
                'label' => 'Price per Bidding Connect ($)',
                'description' => 'USD cost per connect when freelancers purchase proposal submission tokens (e.g. 10 connects = $1.50).',
            ],
            [
                'key' => 'free_signup_connects',
                'value' => '50',
                'type' => 'integer',
                'group' => 'monetization',
                'label' => 'Free Signup Connects',
                'description' => 'Complimentary bidding tokens credited to new freelancer accounts upon registration.',
            ],
            [
                'key' => 'featured_job_price_usd',
                'value' => '29.99',
                'type' => 'float',
                'group' => 'monetization',
                'label' => 'Featured Job Listing Price ($)',
                'description' => 'Upcharge fee charged to clients to highlight their job on the homepage and pin to top of browse feed.',
            ],
            [
                'key' => 'boost_proposal_connects',
                'value' => '10',
                'type' => 'integer',
                'group' => 'monetization',
                'label' => 'Boost Proposal Connects (Tokens)',
                'description' => 'Additional bidding tokens required for a freelancer to promote their proposal to the top of client inbox.',
            ],
            [
                'key' => 'min_payout_amount',
                'value' => '50.0',
                'type' => 'float',
                'group' => 'payouts',
                'label' => 'Minimum Payout Threshold ($)',
                'description' => 'Minimum wallet balance required for freelancers to initiate a bank/PayPal withdrawal.',
            ],
            [
                'key' => 'payout_fixed_fee',
                'value' => '1.50',
                'type' => 'float',
                'group' => 'payouts',
                'label' => 'Fixed Withdrawal Fee ($)',
                'description' => 'Flat transaction processing fee deducted per approved payout request.',
            ],
            [
                'key' => 'top_rated_earnings_threshold',
                'value' => '1000.0',
                'type' => 'float',
                'group' => 'reputation',
                'label' => 'Top Rated Earnings Requirement ($)',
                'description' => 'Minimum total marketplace earnings needed for a freelancer to receive the ⭐ Top Rated badge.',
            ],
            [
                'key' => 'top_rated_plus_earnings_threshold',
                'value' => '10000.0',
                'type' => 'float',
                'group' => 'reputation',
                'label' => 'Top Rated Plus Earnings Requirement ($)',
                'description' => 'Earnings threshold required to achieve the 👑 Top Rated Plus elite enterprise tier.',
            ],
            [
                'key' => 'min_jss_for_badge',
                'value' => '90',
                'type' => 'integer',
                'group' => 'reputation',
                'label' => 'Minimum Job Success Score (JSS %)',
                'description' => 'Minimum client satisfaction rating required to maintain Top Rated status.',
            ],
        ];

        foreach ($defaults as $data) {
            self::firstOrCreate(['key' => $data['key']], $data);
        }
    }
}
