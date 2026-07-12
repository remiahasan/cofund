<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignImage;
use Illuminate\Database\Seeder;

class CampaignImageSeeder extends Seeder
{
    public function run(): void
    {
        foreach(Campaign::all() as $campaign){

            CampaignImage::create([
                'campaign_id'=>$campaign->id,
                'url'=>"https://picsum.photos/800/600?random=".$campaign->id,
                'is_primary'=>true
            ]);

            CampaignImage::create([
                'campaign_id'=>$campaign->id,
                'url'=>"https://picsum.photos/800/600?random=".($campaign->id+20),
                'is_primary'=>false
            ]);
        }
    }
}