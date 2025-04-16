<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class OrderService
{
    public function __construct(public FeeService $feeService)
    {
    }

    public function storeOrder(User $user, array $data): Order
    {
        $feePercent = $this->feeService->calculateFeePercent($data['quantity']);
        return Order::query()->create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'price' => $data['price'],
            'total_quantity' => $data['quantity'],
            'remaining_quantity' => $data['quantity'],
            'status' => OrderStatus::OPEN->value,
            'fee_percent' => $feePercent
        ]);
    }

    public function cancelOrder(Order $order): void
    {
        $order->update([
            'status' => OrderStatus::CANCELED->value,
        ]);
    }

    public function getOrders(User $user, array $statuses, string $type = null)
    {
        return $user->orders()
            ->whereIn('status', $statuses)
            ->when(!is_null($type), function (Builder $q) use ($type) {
                $q->where('type', $type);
            });
    }
}
