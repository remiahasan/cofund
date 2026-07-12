<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignTier;
use Illuminate\Database\Seeder;

class CampaignTierSeeder extends Seeder
{
    public function run(): void
    {
        foreach(Campaign::all() as $campaign){

            CampaignTier::create([
                'campaign_id'=>$campaign->id,
                'name'=>'Silver',
                'minimum_amount'=>50000,
                'quota'=>100,
                'remaining_quota'=>80,
                'reward_description'=>'Sticker'
            ]);

            CampaignTier::create([
                'campaign_id'=>$campaign->id,
                'name'=>'Gold',
                'minimum_amount'=>150000,
                'quota'=>50,
                'remaining_quota'=>35,
                'reward_description'=>'T-Shirt'
            ]);

            CampaignTier::create([
                'campaign_id'=>$campaign->id,
                'name'=>'Platinum',
                'minimum_amount'=>500000,
                'quota'=>20,
                'remaining_quota'=>10,
                'reward_description'=>'Exclusive Merchandise'
            ]);

        }
    }
}
