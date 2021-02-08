<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Mail\Events\MessageSent;
use App\Models\Mail;

class MailSent implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        Mail::create([
            'email' => $event->data['email'],
            'subject' => $event->data['subject'],
            'body' => $event->data['body'],
            'attachments' => json_encode($event->data['attachments'])
        ]);
    }
}
