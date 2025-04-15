<?php

namespace App\Services;

use App\Enums\OrderType;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TransactionService
{
    public function __construct(public CommissionService $commissionService)
    {
    }

    public function storeTransaction(Order $order, Order $match, float $matchableQuantity): Transaction
    {
        $totalAmount = $matchableQuantity * $order->price;
        $commission = $this->commissionService->calculate($matchableQuantity, $totalAmount);
        $finalAmount = $totalAmount + $commission['value'];

        return Transaction::query()->create([
            'sell_order_id' => $order->type == OrderType::SELL->value ? $order->id : $match->id,
            'buy_order_id' => $order->type == OrderType::BUY->value ? $order->id : $match->id,
            'seller_id' => $order->type == OrderType::SELL->value ? $order->user_id : $match->user_id,
            'buyer_id' => $order->type == OrderType::BUY->value ? $order->user_id : $match->user_id,
            'trade_quantity' => $matchableQuantity,
            'price' => $order->price,
            'total_amount' => $totalAmount,
            'commission_percent' => $commission['percent'],
            'commission_value' => $commission['value'],
            'final_amount' => $finalAmount,
        ]);
    }

    public function getTransactions(User $user, string $type = null): Builder
    {
        $transactions = Transaction::query();
        if ($type == OrderType::SELL->value) {
            $transactions->where('seller_id', $user->id);
        } elseif ($type == OrderType::BUY->value) {
            $transactions->where('buyer_id', $user->id);
        } else {
            $transactions->where('seller_id', $user->id)
                ->orWhere('buyer_id', $user->id);
        }
        return $transactions->latest();
    }
}
