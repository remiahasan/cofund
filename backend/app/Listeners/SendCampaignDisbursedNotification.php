<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\NotificationService;
use App\Events\CampaignDisbursed;
use App\Mail\CampaignDisbursedMail;
use Illuminate\Support\Facades\Mail;

class SendCampaignDisbursedNotification implements ShouldQueue
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
    public function handle(CampaignDisbursed $event): void
    {
        app(NotificationService::class)
            ->sendCampaignDisbursed($event->campaign);

        Mail::to($event->campaign->creator->email)->queue(new CampaignDisbursedMail($event->campaign));
    }
}
