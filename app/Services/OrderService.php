<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
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
}
