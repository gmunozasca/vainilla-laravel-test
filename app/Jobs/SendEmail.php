<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendNewMail;
use App\Exceptions\InvalidDataSendMailJobException;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
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
        $validator = Validator::make($this->data, [
            'email' => 'required|string|email:rfc,dns|max:320',
            'subject' => 'required|string|max:78',
            'body' => 'required',
            'attachments' => 'array',
            'attachments.*.filename_storage' => 'required|string|max:255',
            'attachments.*.filename' => 'required|string|max:255',
            'attachments.*.mime' => 'required|string|max:127'
        ]);
        if ($validator->fails()) {
            throw new InvalidDataSendMailJobException($validator->errors());
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->data['email'])->send(new SendNewMail(
            $this->data['email'], 
            $this->data['subject'], 
            $this->data['body'], 
            $this->data['attachments']
        ));
    }
}
