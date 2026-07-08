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
            'tier'=>[
                'id'=>$this->tier->id,
                'title'=>$this->tier->title,
                'amount'=>$this->tier->amount,
            ],
            'user'=>[
                'id'=>$this->user->id,
                'name'=>$this->user->name,
            ],
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }
}
