<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:reset-admin {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the password for the admin user or create it if not exists.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->argument('username');
        $password = Str::random(10);
        $user = Admin::where('username', $username)->first();

        if (!$user) {
            // Create a new user if the admin user doesn't exist
            $user = Admin::create([
                'username' => $username,
                'password' => Hash::make($password),
                'is_2fa' => '0',
                'rules' => '{"isAdmin": true}'
            ]);

            $this->info('User ' . $user->username . ' created with the password: ' . $password . ' successfully');
        } else {
            // Change the password if the admin user exists
            $this->info('This user is already exists. We can\'t create it again.');
        }
    }
}
