<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendEmail;
use App\Exceptions\InvalidDataSendMailJobException;

class SendMailJobTest extends TestCase
{
    
    public function test_dispatch_send_mail_job()
    {
        $this->expectsJobs(SendEmail::class);

        $filename = Str::random(120);
        Storage::fake($filename);

        $email = Str::random(10).'@gmail.com';
        $subject = Str::random(50);
        $body = Str::random(200);
        $attachments = [
            [
                'filename_storage' => $filename,
                'filename' => Str::random(100),
                'mime' => Str::random(100)
            ]
        ];
        SendEmail::dispatch($email, $subject, $body, $attachments);
    }

    public function test_dispatch_send_mail_job_with_invalid_data()
    {
        $this->expectException(InvalidDataSendMailJobException::class);

        $filename = Str::random(120);
        Storage::fake($filename);

        $email = Str::random(1000).'@gmail.com';
        $subject = 1000;
        $body = Str::random(200);
        $attachments = [
            [
                'filename' => Str::random(100),
                'mime' => Str::random(100)
            ]
        ];
        SendEmail::dispatch($email, $subject, $body, $attachments);
    }
}