<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'balance' => $this->balance,
            'creator_request_status' => $this->creator_request_status,
            'campaign_count' => $this->campaigns_count,
            'backing_count' => $this->backings_count,
            'transaction_count' => $this->transactions_count,

            'created_at' => $this->created_at,
        ];
    }
}
