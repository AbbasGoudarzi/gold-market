<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class MatchOrder implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(
        OrderService       $orderService,
        TransactionService $transactionService,
        UserService        $userService
    ): void
    {
        $order = $this->order->fresh(); // Ensuring that information is update!

        if ($order->remaining_quantity == 0 || $order->status !== 'open') {
            return;
        }

        $matches = $orderService->getMatches($order);

        foreach ($matches as $match) {
            if ($order->remaining_quantity == 0) break;

            $matchableQuantity = min($order->remaining_quantity, $match->remaining_quantity);

            DB::transaction(function () use ($order, $match, $matchableQuantity, $transactionService, $userService) {
                $order->remaining_quantity -= $matchableQuantity;
                $match->remaining_quantity -= $matchableQuantity;

                if ($order->remaining_quantity == 0) $order->status = 'completed';
                if ($match->remaining_quantity == 0) $match->status = 'completed';

                $order->save();
                $match->save();

                $transaction = $transactionService->storeTransaction($order, $match, $matchableQuantity);

                $userService->updateBalance($transaction);
            });
        }
    }
}
