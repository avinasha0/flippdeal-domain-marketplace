<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class VerifyUserEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:verify-email {user_id : The ID of the user to verify}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually verify a user\'s email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }
        
        $this->info("Found user: {$user->name} ({$user->email})");
        
        if ($user->hasVerifiedEmail()) {
            $this->info("User's email is already verified.");
            return 0;
        }
        
        $user->markEmailAsVerified();
        
        $this->info("Email verification status updated successfully!");
        $this->info("User's email is now verified.");
        
        return 0;
    }
}
