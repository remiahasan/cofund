<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Campaign;
use App\Models\CampaignImage;
use App\Models\CampaignUpdate;
use App\Models\CampaignTier;
use App\Models\Backing;
use App\Models\Transaction;
use App\Models\Notification;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            CampaignSeeder::class,
            CampaignImageSeeder::class,
            CampaignUpdateSeeder::class,
            CampaignTierSeeder::class,
            BackingSeeder::class,
            TransactionSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}