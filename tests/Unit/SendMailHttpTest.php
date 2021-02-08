<?php

namespace Tests\Unit;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;
use App\Models\User;

class SendMailHttpTest extends TestCase
{
    
    public function test_a_valid_send_mail()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('/api/send', [
            'emails' => [
                [
                    'email' => Str::random(10).'@gmail.com',
                    'subject' => Str::random(50),
                    'body' => Str::random(200),
                    'attachments' => [
                        [
                            'filename' => Str::random(100),
                            'mime' => Str::random(100),
                            'base64' => Str::random(200)
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200)
            ->assertSee('The emails have been sent');
    }
    
    public function test_a_valid_send_mail_without_attachments()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('/api/send', [
            'emails' => [
                [
                    'email' => Str::random(10).'@gmail.com',
                    'subject' => Str::random(50),
                    'body' => Str::random(200)
                ]
            ]
        ]);

        $response->assertStatus(200)
            ->assertSee('The emails have been sent');
    }
    
    public function test_with_invalid_data_for_send_mail()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('/api/send', [
            'emails' => [
                [
                    'email' => Str::random(1000).'@gmail.com',
                    'subject' => 1000,
                    'body' => Str::random(200)
                ]
            ]
        ]);

        $response->assertStatus(400)
            ->assertSee('email may not be greater than 320 characters')
            ->assertSee('subject must be a string');
    }
}
