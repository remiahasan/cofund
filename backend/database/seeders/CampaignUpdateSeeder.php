<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignUpdate;
use Illuminate\Database\Seeder;

class CampaignUpdateSeeder extends Seeder
{
    public function run(): void
    {
        foreach(Campaign::all() as $campaign){
            CampaignUpdate::create([
                'campaign_id'=>$campaign->id,
                'title'=>'Progress Minggu Pertama',
                'content'=>'Terima kasih kepada seluruh pendukung.'
            ]);

            CampaignUpdate::create([
                'campaign_id'=>$campaign->id,
                'title'=>'Target 50%',
                'content'=>'Campaign sudah mencapai 50% pendanaan.'
            ]);
        }
    }
}
