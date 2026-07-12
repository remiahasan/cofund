<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        for($i=1;$i<=8;$i++){

            Campaign::create([
                'user_id'=>2,
                'category_id'=>rand(1,6),
                'title'=>"Campaign $i",
                'slug'=>"campaign-$i",
                'description'=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                'target_amount'=>rand(5000000,50000000),
                'collected_amount'=>rand(500000,3000000),
                'deadline'=>now()->addDays(rand(20,90)),
                'status'=>'active'
            ]);

        }
    }
}