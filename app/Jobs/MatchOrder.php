<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderService;
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
    public function handle(OrderService $orderService): void
    {
        $order = $this->order->fresh(); // Ensuring that information is update!

        if ($order->remaining_quantity == 0 || $order->status !== 'open') {
            return;
        }

        $matches = $orderService->getMatches($order);

        foreach ($matches as $match) {
            if ($order->remaining_quantity == 0) break;

            $matchable = min($order->remaining_quantity, $match->remaining_quantity);

            DB::transaction(function () use ($order, $match, $matchable) {
                $order->remaining_quantity -= $matchable;
                $match->remaining_quantity -= $matchable;

                if ($order->remaining_quantity == 0) $order->status = 'completed';
                if ($match->remaining_quantity == 0) $match->status = 'completed';

                $order->save();
                $match->save();

                // TODO: Store in transactions table
            });
        }
    }
}
