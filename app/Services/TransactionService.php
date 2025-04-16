<?php

namespace App\Services;

use App\Enums\OrderType;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TransactionService
{
    public function __construct(public FeeService $feeService)
    {
    }

    public function storeTransaction(Order $buyOrder, Order $sellOrder, float $quantity): Transaction
    {
        $totalAmount = $quantity * $buyOrder->price; // or sellOrder->price
        $sellerFee = $this->feeService->calculateFeeValue($sellOrder->fee_percent, $totalAmount);
        $buyerFee = $this->feeService->calculateFeeValue($buyOrder->fee_percent, $totalAmount);
        $sellerFinalAmount = $totalAmount + $sellerFee;
        $buyerFinalAmount = $totalAmount + $buyerFee;

        return Transaction::query()->create([
            'sell_order_id' => $sellOrder->id,
            'buy_order_id' => $buyOrder->id,
            'seller_id' => $sellOrder->user_id,
            'buyer_id' => $buyOrder->user_id,
            'trade_quantity' => $quantity,
            'price' => $buyOrder->price,
            'total_amount' => $totalAmount,
            'seller_fee' => $sellerFee,
            'buyer_fee' => $buyerFee,
            'seller_final_amount' => $sellerFinalAmount,
            'buyer_final_amount' => $buyerFinalAmount,
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
