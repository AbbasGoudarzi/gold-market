<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Transaction;

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
            'sell_order_id' => $order->type == 'sell' ? $order->id : $match->id,
            'buy_order_id' => $order->type == 'buy' ? $order->id : $match->id,
            'trade_quantity' => $matchableQuantity,
            'price' => $order->price,
            'total_amount' => $totalAmount,
            'commission_percent' => $commission['percent'],
            'commission_value' => $commission['value'],
            'final_amount' => $finalAmount,
        ]);
    }
}
