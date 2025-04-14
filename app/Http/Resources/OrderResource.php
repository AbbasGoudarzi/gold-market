<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'type' => $this->type,
            'total_quantity' => $this->total_quantity,
            'remaining_quantity' => $this->remaining_quantity,
            'status' => $this->status,
            'price' => $this->price / 10,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
