<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\NotificationService;
use App\Events\CampaignRejected;
use App\Mail\CampaignRejectedMail;
use Illuminate\Support\Facades\Mail;

class SendCampaignRejectedNotification implements ShouldQueue
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
    public function handle(CampaignRejected $event): void
    {
        app(NotificationService::class)
            ->sendCampaignRejected($event->campaign);

        Mail::to($event->campaign->creator->email)->queue(new CampaignRejectedMail($event->campaign, $event->reason));    
    }
}
