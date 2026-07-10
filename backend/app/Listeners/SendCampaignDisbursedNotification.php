<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\NotificationService;
use App\Events\CampaignDisbursed;

class SendCampaignDisbursedNotification
{
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
    public function handle(object $event): void
    {
        app(NotificationService::class)
            ->sendCampaignDisbursed($event->campaign);
    }
}
