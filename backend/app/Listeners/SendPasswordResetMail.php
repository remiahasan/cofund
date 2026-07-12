<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\PasswordResetRequested;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetMail implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(PasswordResetRequested $event): void
    {
        Mail::to($event->user->email)->queue(new PasswordResetMail($event->user, $event->token));
    }
}