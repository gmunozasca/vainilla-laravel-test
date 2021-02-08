<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Jobs\SendEmail;
use App\Models\Mail;
use Illuminate\Support\Facades\Crypt;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware('auth:sanctum')->post('/send', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'emails' => 'required|array|min:1',
        'emails.*.email' => 'required|string|email:rfc,dns|max:320',
        'emails.*.subject' => 'required|string|max:78',
        'emails.*.body' => 'required',
        'emails.*.attachments' => 'array',
        'emails.*.attachments.*.filename' => 'required|string|max:255',
        'emails.*.attachments.*.mime' => 'required|string|max:127',
        'emails.*.attachments.*.base64' => 'required|string'
    ]);
    if ($validator->fails()) {
        abort(response()->json($validator->errors(), 400));
    }
    foreach ($request->input('emails') as $data) {
        $email = $data['email'];
        $subject = $data['subject'];
        $body = $data['body'];
        $attachments = [];

        if (array_key_exists('attachments', $data)) {
            $preAttachments = $data['attachments'];
        
            if ($preAttachments and !empty($preAttachments)) {
                foreach ($preAttachments as $attachment) {
                    $filename_storage = Carbon::now()->timestamp . '_' . $attachment['filename'];
                    Storage::disk('local')->put($filename_storage, base64_decode($attachment['base64']), 'public');
                    $attachments[] = [
                        "filename_storage" => $filename_storage,
                        "filename" => $attachment['filename'],
                        "mime" => $attachment['mime']
                    ];
                }
            }
        }

        SendEmail::dispatch($email, $subject, $body, $attachments);
    }
    return response('The emails have been sent');
});

Route::middleware('auth:sanctum')->get('/list', function (Request $request) {
    $mails = Mail::orderBy('created_at', 'desc')->get();
    $result = [];
    foreach ($mails as $mail) {
        $item = [
            'email' => $mail->email,
            'subject' => $mail->subject,
            'body' => $mail->body
        ];
        if ($mail->attachments) {
            $attachments = [];
            $preAttachments = json_decode($mail->attachments);
            foreach ($preAttachments as $attachment) {
                $downloadData = [
                    'filename' => $attachment->filename,
                    'filename_storage' => $attachment->filename_storage
                ];
                $encrypted = Crypt::encryptString(json_encode($downloadData));
                $attachments[] = route('download', [ 'token' => $encrypted ]);
            }
            $item['attachments'] = $attachments;
        }
        $result[] = $item;
    }
    return response()->json($result);
});