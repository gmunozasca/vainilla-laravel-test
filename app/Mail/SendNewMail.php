<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Mail;
use Illuminate\Support\Facades\Log;

class SendNewMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $body, $attachments = false)
    {
        $this->data = [
            "email" => $email,
            "subject" => $subject,
            "body" => $body,
            "attachments" => $attachments
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message = $this->markdown('emails.new_email')
            ->subject($this->data['subject'])
            ->with($this->data);
            
        if ($this->data['attachments'] and !empty($this->data['attachments'])) {
            foreach ($this->data['attachments'] as $attachment) {
                $message->attach(Storage::path($attachment['filename_storage']), [
                    'as' => $attachment['filename'],
                    'mime' => $attachment['mime']
                ]);
            }
        }
        
        return $message;
    }
}
