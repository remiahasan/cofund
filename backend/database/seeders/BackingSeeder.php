<?php

namespace Database\Seeders;

use App\Models\Backing;
use App\Models\Campaign;
use App\Models\CampaignTier;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BackingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('id', '!=', 1)->take(15)->get();

        foreach(Campaign::all() as $campaign){

            Backing::create([
                'user_id'=>3,
                'campaign_id'=>$campaign->id,
                'campaign_tier_id'=>$campaign->tiers()->first()->id,
                'amount'=>100000,
                'status'=>'completed'
            ]);

        }
    }
}
