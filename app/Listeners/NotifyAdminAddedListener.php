<?php

namespace App\Listeners;

use App\Events\AdminAddedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAdminAddedListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(AdminAddedEvent $event)
    {
        $user = $event->user;

        Mail::send('admin.adminAdded', [], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('You have been added to the Admin App!');
        });
    }
}
