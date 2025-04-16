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
        $userIsSeller = $this->seller_id == Auth::id();
        return [
            'id' => $this->id,
            'type' => $userIsSeller ? OrderType::SELL->value : OrderType::BUY->value,
            'quantity' => $this->trade_quantity,
            'price' => $this->price / 10,
            'total_amount' => $this->total_amount / 10,
            'fee_value' => $userIsSeller ? $this->seller_fee / 10 : $this->buyer_fee / 10,
            'final_amount' => $userIsSeller ? $this->seller_final_amount / 10 : $this->buyer_final_amount / 10,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
