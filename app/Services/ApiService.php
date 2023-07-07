<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * You don't need to do anything here. This is just to help
 */
class ApiService
{
    /**
     * Create a new discount code for an affiliate
     *
     * @param Merchant $merchant
     *
     * @return array{id: int, code: string}
     */
    public function createDiscountCode(Merchant $merchant): array
    {
        return [
            'id' => rand(0, 100000),
            'code' => Str::uuid()
        ];
    }

    /**
     * Send a payout to an email
     *
     * @param  string $email
     * @param  float $amount
     * @return void
     * @throws RuntimeException
     */
    public function sendPayout(string $email, float $amount)
    {
        // Implement the logic to send the payout to the affiliate
        // This could involve making an API request, interacting with a payment gateway, or any other appropriate process

        // For demonstration purposes, we'll simply log the payout details
        $payoutDetails = [
            'affiliate_email' => $email,
            'amount' => $amount,
            'date' => now()->toDateTimeString(),
        ];

        // Log the payout details
        Log::info('Payout sent:', $payoutDetails);
    }
}
