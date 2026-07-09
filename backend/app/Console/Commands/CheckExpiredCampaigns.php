<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DisburseCampaignJob;
use App\Jobs\RefundBackersJob;
use App\Models\Campaign;
use Carbon\Carbon;

class CheckExpiredCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check expired campaigns and dispatch jobs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Campaign::where('status', 'active')
            ->whereDate('deadline', '<', now())
            ->each(function (Campaign $campaign) {
                if ($campaign->collected_amount >= $campaign->target_amount) {
                    $campaign->update([
                        'status' => 'success'
                    ]);
                    DisburseCampaignJob::dispatch($campaign);
                } else {
                    $campaign->update([
                        'status' => 'failed'
                    ]);
                    RefundBackersJob::dispatch($campaign);
                }
            });
        return self::SUCCESS;
    }
}
