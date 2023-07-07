<?php

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Pass the necessary data to the process order method.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Get the necessary data from the request
        $data = $request->all();

        // Process the order using the order service
        $this->orderService->processOrder($data);

        // Return a JSON response indicating the successful processing of the order
        return response()->json(['message' => 'Order processed successfully']);
    }
}
