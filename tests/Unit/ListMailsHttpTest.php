<?php

namespace Tests\Unit;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;
use App\Models\User;

class ListMailsTest extends TestCase
{
    
    public function test_list_mails()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $mail = [
            'email' => Str::random(10).'@gmail.com',
            'subject' => Str::random(50),
            'body' => Str::random(10),
            'attachments' => [
                [
                    'filename' => Str::random(10),
                    'mime' => Str::random(10),
                    'base64' => Str::random(10)
                ]
            ]
        ];

        $this->postJson('/api/send', [ 'emails' => [ $mail ] ]);

        $response = $this->get('/api/list');

        $response->assertStatus(200);

        $last_item = json_decode($response->getContent())[0];

        $this->assertEquals($last_item->email, $mail['email']);
        $this->assertEquals($last_item->subject, $mail['subject']);
        $this->assertEquals($last_item->body, $mail['body']);
        $this->assertStringContainsString('/download/', $last_item->attachments[0]);
    }
}
