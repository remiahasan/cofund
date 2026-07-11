<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\NotificationService;
use App\Events\BackingConfirmed;
use Illuminate\Support\Facades\Mail;
use App\Mail\BackingConfirmedMail;

class SendBackingConfirmedNotification implements ShouldQueue
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
    public function handle(BackingConfirmed $event): void
    {
        app(NotificationService::class)
            ->sendBackingConfirmed($event->backing);

        Mail::to($event->backing->user->email)->queue(new BackingConfirmedMail($event->backing));
    }
}
