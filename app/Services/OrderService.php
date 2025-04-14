<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    public function storeOrder(User $user, array $data)
    {
        return Order::query()->create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'price' => $data['price'],
            'total_quantity' => $data['quantity'],
            'remaining_quantity' => $data['quantity'],
            'status' => 'open',
        ]);
    }

    public function getMatches(Order $order): Collection
    {
        $oppositeType = $order->type == 'buy' ? 'sell' : 'buy';

        return Order::query()->where('type', $oppositeType)
            ->where('price', $order->price)
            ->where('status', 'open')
            ->where('user_id', '!=', $order->user_id)
            ->orderBy('created_at')
            ->get();
    }

    public function cancelOrder(Order $order): void
    {
        $order->update([
            'status' => 'cancelled',
        ]);
    }
}
