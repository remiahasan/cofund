<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\NotificationService;
use App\Events\CampaignDeadlineReminder;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignDeadlineReminderMail;

class SendCampaignDeadlineReminder implements ShouldQueue
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
    public function handle(CampaignDeadlineReminder $event): void
    {
        app(NotificationService::class)
            ->sendCampaignDeadlineReminder($event->campaign,$event->days);

        if ($event->days !== 1) {
            return;
        }

        foreach ($event->campaign->backings as $backing) {

            Mail::to($backing->user->email)->queue(
                new CampaignDeadlineReminderMail(
                    $event->campaign,
                    $event->days
                )
            );
        }
    }
}
