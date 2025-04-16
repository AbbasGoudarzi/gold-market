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

    public function storeTransaction(Order $buyOrder, Order $sellOrder, float $quantity): Transaction
    {
        $totalAmount = $quantity * $buyOrder->price;
        $commission = $this->commissionService->calculate($quantity, $totalAmount);
        $finalAmount = $totalAmount + $commission['value'];

        return Transaction::query()->create([
            'sell_order_id' => $sellOrder->id,
            'buy_order_id' => $buyOrder->id,
            'seller_id' => $sellOrder->user_id,
            'buyer_id' => $buyOrder->user_id,
            'trade_quantity' => $quantity,
            'price' => $buyOrder->price,
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
