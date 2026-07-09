<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Services\NotificationService;
use Carbon\Carbon;

class NotifyDeadlineApproaching extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:notify-deadline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify backers when campaign deadline is approaching';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Campaign::where('status', 'active')
            ->whereIn('deadline', [
                now()->addDays(3)->toDateString(),
                now()->addDay()->toDateString(),
            ])
            ->each(function (Campaign $campaign) {
                NotificationService::sendDeadlineNotification(
                    $campaign
                );
            });
        return self::SUCCESS;
    }
}
