<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\Campaign;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notification::create([
            'user_id'=>3,
            'type'=>'backing',
            'title'=>'Backing Berhasil',
            'body'=>'Terima kasih telah mendukung campaign.',
            'data'=>json_encode([
                'campaign_id'=>1
            ])
        ]);

        Notification::create([
            'user_id'=>2,
            'type'=>'campaign',
            'title'=>'Campaign Disetujui',
            'body'=>'Campaign Anda berhasil dipublikasikan.',
            'data'=>json_encode([
                'campaign_id'=>1
            ])
        ]);
    }
}
