<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\CampaignApproved;
use App\Events\CampaignRejected;
use App\Events\NewBackingCreated;
use App\Events\BackingConfirmed;
use App\Events\CampaignUpdated;
use App\Events\CampaignDeadlineReminder;
use App\Events\CampaignDisbursed;
use App\Events\CampaignRefunded;

use App\Listeners\SendCampaignApprovedNotification;
use App\Listeners\SendCampaignRejectedNotification;
use App\Listeners\SendNewBackingNotification;
use App\Listeners\SendBackingConfirmedNotification;
use App\Listeners\SendCampaignUpdatedNotification;
use App\Listeners\SendCampaignDeadlineReminder;
use App\Listeners\SendCampaignDisbursedNotification;
use App\Listeners\SendCampaignRefundedNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CampaignApproved::class => [
            SendCampaignApprovedNotification::class,
        ],
        CampaignRejected::class => [
            SendCampaignRejectedNotification::class,
        ],
        NewBackingCreated::class => [
            SendNewBackingNotification::class,
        ],
        BackingConfirmed::class => [
            SendBackingConfirmedNotification::class,
        ],
        CampaignUpdated::class => [
            SendCampaignUpdatedNotification::class,
        ],
        CampaignDeadlineReminder::class => [
            SendCampaignDeadlineReminder::class,
        ],
        CampaignDisbursed::class => [
            SendCampaignDisbursedNotification::class,
        ],
        CampaignRefunded::class => [
            SendCampaignRefundedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
