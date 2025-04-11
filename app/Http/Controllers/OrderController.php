<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Jobs\MatchOrder;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(public OrderService $orderService)
    {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $order = $this->orderService->storeOrder($user, $request->all());

        MatchOrder::dispatch($order);

        return response()->json([
            'message' => 'Order created and matching started in queue.',
            'data' => [
                'order' => $order,
            ]
        ], 201);
    }
}

