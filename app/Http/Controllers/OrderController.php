<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Jobs\MatchOrder;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(public OrderService $orderService, public UserService $userService)
    {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = $request->user();
        if($request->get('type') == 'sell') {
            if(!$this->userService->checkGoldBalance($user, $request->get('quantity'))) {
                return response()->json(['message' => 'Insufficient gold balance to place this sell order.'], 422);
            }
        } else {
            // check money balance!, for type == 'buy'
        }
        $order = $this->orderService->storeOrder($user, $request->all());

        MatchOrder::dispatch($order);

        return response()->json([
            'message' => 'Order created and matching started in queue.',
            'data' => [
                'order' => $order,
            ]
        ], 201);
    }

    public function cancel(Request $request, int $orderId): JsonResponse
    {
        $user = $request->user();
        $order = Order::query()->findOrFail($orderId);

        if ($user->cannot('cancel', $order)) {
            return response()->json(['message' => 'You do not have access to this order.'], 403);
        }
        if ($order->status != 'open') {
            return response()->json(['message' => 'Only orders in open status can be cancelled.'], 422);
        }

        $this->orderService->cancelOrder($order);

        return response()->json([
            'message' => 'The order was successfully canceled.',
            'data' => [
                'order' => $order,
            ]
        ]);
    }
}

