<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Transaction;

class TransactionService
{
    public function storeTransaction(Order $order, Order $match, float $matchableQuantity): Transaction
    {
        $totalAmount = $matchableQuantity * $order->price;
        $commission = $this->calcCommission($matchableQuantity, $totalAmount);
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

    public function calcCommission(float $quantity, int $totalAmount): array
    {
        $minCommission = 500000;
        $maxCommission = 50000000;
        $commissionPercent = match (true) {
            $quantity < 1 => 2,
            ($quantity >= 1 && $quantity < 10) => 1.5,
            $quantity >= 10 => 1
        };

        $commissionValue = $totalAmount * $commissionPercent / 100;
        $commissionValue = max($commissionValue, $minCommission);
        $commissionValue = min($commissionValue, $maxCommission);
        return [
            'percent' => $commissionPercent,
            'value' => $commissionValue,
        ];
    }
}
