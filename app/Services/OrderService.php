<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function storeOrder(User $user, array $data): Order
    {
        return Order::query()->create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'price' => $data['price'],
            'total_quantity' => $data['quantity'],
            'remaining_quantity' => $data['quantity'],
            'status' => OrderStatus::OPEN->value,
        ]);
    }

    public function cancelOrder(Order $order): void
    {
        $order->update([
            'status' => OrderStatus::CANCELED->value,
        ]);
    }
}
