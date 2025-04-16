<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $sell_order_id
 * @property int $buy_order_id
 * @property int $seller_id
 * @property int $buyer_id
 * @property float $trade_quantity
 * @property int $price
 * @property int $total_amount
 * @property int $seller_fee
 * @property int $buyer_fee
 * @property int $seller_final_amount
 * @property int $buyer_final_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Order|null $buyOrder
 * @property-read User|null $buyer
 * @property-read Order|null $sellOrder
 * @property-read User|null $seller
 */
class Transaction extends Model
{
    protected $guarded = [];

    public function buyOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'buy_order_id');
    }

    public function sellOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'sell_order_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
