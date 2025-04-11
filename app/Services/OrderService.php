<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function storeOrder(User $user, array $data)
    {
        return DB::transaction(function () use ($data, $user) {
            return Order::query()->create([
                'user_id' => $user->id,
                'type' => $data['type'],
                'price' => $data['price'],
                'total_quantity' => $data['quantity'],
                'remaining_quantity' => $data['quantity'],
                'status' => 'open',
            ]);
        });
    }

    public function getMatches(Order $order): Collection
    {
        $oppositeType = $order->type === 'buy' ? 'sell' : 'buy';
//        $priceCondition = $order->type === 'buy' ? '<=' : '>=';
        $priceCondition = '=';
        $sortDirection = $order->type === 'buy' ? 'asc' : 'desc';

        return Order::query()->where('type', $oppositeType)
            ->where('price', $priceCondition, $order->price)
            ->where('status', 'open')
            ->orderBy('price', $sortDirection)
            ->orderBy('created_at')
            ->get();
    }
}
