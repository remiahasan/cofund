<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CampaignTierResource;
use App\Http\Resources\CampaignUpdateResource;

class CampaignResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'collected_amount' => $this->collected_amount,
            'target_amount' => $this->target_amount,
            'deadline' => $this->deadline,
            'status' => $this->status,
            'video_url' => $this->video_url,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'creator' => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name,
                'email' => $this->creator?->email,
            ],
            'images' => $this->images->map(function ($image) {
                return [
                    'id'=>$image->id,
                    'url'=>asset('storage/'.$image->url),
                    'is_primary'=>$image->is_primary,
                ];
            }),
            'updates' => CampaignUpdateResource::collection($this->whenLoaded('updates')),
            'tiers' => CampaignTierResource::collection($this->whenLoaded('tiers')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
