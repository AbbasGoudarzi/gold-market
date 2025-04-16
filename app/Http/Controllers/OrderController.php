<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Events\NewOrderCreated;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        public OrderService $orderService,
        public UserService  $userService)
    {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = $request->user();
        $requestData = $request->all();
        $requestData['price'] = $requestData['price'] * 10; // Toman to rial

        if ($requestData['type'] == OrderType::SELL->value) {
            if (!$this->userService->checkGoldBalance($user, $requestData['quantity'])) {
                return response()->json(['message' => 'Insufficient gold balance to place this sell order.'], 422);
            }
        }
        $order = $this->orderService->storeOrder($user, $requestData);

        event(new NewOrderCreated());

        return response()->json([
            'message' => 'Order created and matching started in queue.',
            'data' => [
                'order' => new OrderResource($order->load('user')),
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
        if ($order->status != OrderStatus::OPEN->value) {
            return response()->json(['message' => 'Only orders in open status can be cancelled.'], 422);
        }

        $this->orderService->cancelOrder($order);

        return response()->json([
            'message' => 'The order was successfully canceled.',
            'data' => [
                'order' => new OrderResource($order),
            ]
        ]);
    }
}

