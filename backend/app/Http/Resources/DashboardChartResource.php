<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardCreatorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $percentage = 0;

        if ($this->target_amount > 0) {
            $percentage = round(($this->collected_amount / $this->target_amount) * 100, 2);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'target_amount' => $this->target_amount,
            'collected_amount' => $this->collected_amount,
            'percentage' => $percentage,
            'total_backer' => $this->backings_count,
            'deadline' => $this->deadline,
        ];
    }
}