<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'backing' => [
                'id' => $this->backing->id,
                'campaign_id' => $this->backing->campaign_id,
            ],
            'type' => $this->type,
            'amount' => $this->amount,
            'status' => $this->status,
            'reference_id' => $this->reference_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
