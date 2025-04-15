<?php

namespace App\Http\Resources;

use App\Enums\OrderType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->seller_id == Auth::id() ? OrderType::SELL->value : OrderType::BUY->value,
            'quantity' => $this->trade_quantity,
            'price' => $this->price / 10,
            'total_amount' => $this->total_amount / 10,
            'commission_value' => $this->commission_value / 10,
            'final_amount' => $this->final_amount / 10,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
