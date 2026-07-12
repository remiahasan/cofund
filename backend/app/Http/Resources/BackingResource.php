<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BackingResource extends JsonResource
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
            'amount'=>$this->amount,
            'status'=>$this->status,
            'campaign'=>[
                'id'=>$this->campaign->id,
                'title'=>$this->campaign->title,
                'slug'=>$this->campaign->slug,
            ],
            'tier'=> $this->campaignTier ? [
                'id'=>$this->campaignTier->id,
                'name'=>$this->campaignTier->name,
                'minimum_amount'=>$this->campaignTier->minimum_amount,
            ] : null,
            'user'=>[
                'id'=>$this->user->id,
                'name'=>$this->user->name,
            ],
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }
}
