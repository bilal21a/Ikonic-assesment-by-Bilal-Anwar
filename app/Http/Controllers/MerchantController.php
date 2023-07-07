<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MerchantController extends Controller
{
    private $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Useful order statistics for the merchant API.
     *
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');

        // Retrieve the order statistics from the merchant service
        $orderStats = $this->merchantService->getOrderStatistics($fromDate, $toDate);

        return response()->json([
            'count' => $orderStats['total_orders'],
            'commissions_owed' => round($orderStats['unpaid_commissions'], 2),
            'revenue' => round($orderStats['total_revenue'], 2)
        ]);
    }
}
