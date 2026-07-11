<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\NotificationService;
use App\Events\CampaignRefunded;
use App\Mail\CampaignRefundedMail;
use Illuminate\Support\Facades\Mail;

class SendCampaignRefundedNotification implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CampaignRefunded $event): void
    {
        app(NotificationService::class)
            ->sendCampaignRefunded($event->campaign);

        foreach ($event->campaign->backings as $backing) {

            Mail::to($backing->user->email)
                ->queue(
                    new CampaignRefundedMail($event->campaign)
                );

        }
    }
}
