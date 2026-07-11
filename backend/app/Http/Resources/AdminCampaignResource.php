<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminCampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'creator'=>$this->creator->name,
            'target_amount'=>$this->target_amount,
            'collected_amount'=>$this->collected_amount,
            'status'=>$this->status,
            'deadline'=>$this->deadline,
            'created_at'=>$this->created_at,
        ];
    }
}
