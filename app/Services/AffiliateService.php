<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     * @throws AffiliateCreateException
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // Check if the email is already in use by the merchant
        if ($merchant->user->email === $email) {
            throw new AffiliateCreateException('Email is already in use by the merchant');
        }

        // Check if the email is already in use by existing affiliate
        if (User::whereHas('affiliate', function ($query) use ($merchant, $email) {
            $query->where('merchant_id', $merchant->id)->where('email', $email);
        })->exists()) {
            throw new AffiliateCreateException('Email is already in use by an existing affiliate');
        }
        
        // Call the API service to create the discount code
        $apiResponse = $this->apiService->createDiscountCode($merchant);
        
        if (empty($apiResponse['id'])) {
            throw new AffiliateCreateException('Failed to create discount code');
        }
        
        // Create the user for the affiliate
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'type' => User::TYPE_AFFILIATE,
            'password' => bcrypt(Str::random(16)),
        ]);
        
        // Create the affiliate
        $affiliate = Affiliate::create([
            'merchant_id' => $merchant->id,
            'user_id' => $user->id,
            'commission_rate' => $commissionRate,
            'discount_code' => $apiResponse['code'],
        ]);
        
        // Send email
        Mail::to($email)->send(new AffiliateCreated($affiliate));
        
        return $affiliate;
    }
}
