<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function checkGoldBalance(User $user, float $orderQuantity): bool
    {
        return $orderQuantity <= $user->gold_balance;
    }

    public function updateBalance(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $buyer = $transaction->buyer;
            $buyer->increment('gold_balance', $transaction->trade_quantity);

            $seller = $transaction->seller;
            $seller->decrement('gold_balance', $transaction->trade_quantity);
        });
    }
}
