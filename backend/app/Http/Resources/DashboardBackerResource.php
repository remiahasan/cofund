<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardBackerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_backing'=>$this['total_backing'],
            'campaign_joined'=>$this['campaign_joined'],
            'total_refund'=>$this['total_refund'],
        ];
    }
}
