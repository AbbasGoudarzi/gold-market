<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessOrderMatching implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(TransactionService $transactionService, UserService $userService): void
    {
        // Receive unique price lists from open orders
        $uniquePrices = Order::query()
            ->where(function (builder $query) {
                $query->where('status', OrderStatus::OPEN->value)
                    ->orWhere('status', OrderStatus::PARTIAL->value);
            })
            ->select('price')
            ->distinct()
            ->pluck('price');

        // Processing each price group
        foreach ($uniquePrices as $price) {
            DB::beginTransaction();
            try {
                // Lock orders at this specific price
                $buyOrders = Order::query()->where('type', OrderType::BUY->value)
                    ->where('price', $price)
                    ->where(function (builder $query) {
                        $query->where('status', OrderStatus::OPEN->value)
                            ->orWhere('status', OrderStatus::PARTIAL->value);
                    })
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->get();

                $sellOrders = Order::query()->where('type', OrderType::SELL->value)
                    ->where('price', $price)
                    ->where(function (builder $query) {
                        $query->where('status', 'OPEN')
                            ->orWhere('status', 'PARTIAL');
                    })
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->get();

                // Matching logic for this specific price
                $this->matchOrdersAtPrice($buyOrders, $sellOrders, $transactionService, $userService);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error in matching orders at price {$price}: " . $e->getMessage());
                // Continue to the next price without stopping the entire process.
            }
        }
    }

    private function matchOrdersAtPrice(Collection $buyOrders, Collection $sellOrders, TransactionService $transactionService, UserService $userService): void
    {
        // Matching logic for this specific price
        foreach ($buyOrders as $buyOrder) {
            /** @var Order $buyOrder */
            if ($buyOrder->remaining_quantity == 0) continue;

            foreach ($sellOrders as $sellOrder) {
                /** @var Order $sellOrder */
                if ($sellOrder->remaining_quantity == 0) continue;

                $transactionQuantity = min($buyOrder->remaining_quantity, $sellOrder->remaining_quantity);

                $transaction = $transactionService->storeTransaction($buyOrder, $sellOrder, $transactionQuantity);

                // Update orders
                $buyOrder->remaining_quantity -= $transactionQuantity;
                $sellOrder->remaining_quantity -= $transactionQuantity;

                $buyOrder->status = $buyOrder->remaining_quantity == 0 ? OrderStatus::COMPLETED->value : OrderStatus::PARTIAL->value;
                $sellOrder->status = $sellOrder->remaining_quantity == 0 ? OrderStatus::COMPLETED->value : OrderStatus::PARTIAL->value;

                $buyOrder->save();
                $sellOrder->save();

                $userService->updateBalance($transaction);

                if ($buyOrder->remaining_quantity == 0) {
                    break; // Go to next BUY order
                }
            }
        }
    }
}
