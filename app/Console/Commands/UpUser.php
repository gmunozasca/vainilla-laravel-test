<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UpUser extends Command
{
    protected $signature = 'up:user';

    protected $description = 'Generate a user to consume API with API Token';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $user = User::create([
            'name' => Str::random(10),
            'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('password')
        ]);
        
        $token = $user->createToken('Test API');
        
        $this->info("API Token: $token->plainTextToken");
    }
}
