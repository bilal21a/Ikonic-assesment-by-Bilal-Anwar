<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;

class OrderService
{
    protected AffiliateService $affiliateService;
    protected $merchant;
    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        $order = Order::where('external_order_id', $data['order_id'])->first();

        if (!$order) {
            $merchant = Merchant::where('domain', $data['merchant_domain'])->first();

            if (!$merchant) {
                // Handle the case where the merchant is not found
                return;
            }

            $affiliate = Affiliate::where('email', $data['customer_email'])->first();

            if (!$affiliate) {
                $affiliate = $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], 0.1);
                // error occured here
                // can be fix if i remove tests/Feature/Services/OrderServiceTest.php:57
            }

            $order = new Order();
            $order->external_order_id = $data['order_id'];
            $order->subtotal = $data['subtotal_price'];
            $order->discount_code = $data['discount_code'];
            $order->merchant()->associate($merchant);
            $order->affiliate()->associate($affiliate);
            $order->commission_owed = $data['subtotal_price'] * $affiliate->commission_rate;
            $order->save();

            $this->logCommissions($order);
        }

        return $order;
    }


    /**
     * Log any commissions for the order.
     *
     * @param Order $order
     * @return void
     */
    protected function logCommissions(Order $order)
    {
        // Perform commission logging logic here
    }
}
