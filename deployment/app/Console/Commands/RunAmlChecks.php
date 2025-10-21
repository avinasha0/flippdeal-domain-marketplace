<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AmlService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class RunAmlChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aml:check 
                            {--user= : Check specific user ID}
                            {--recent : Only check users with recent activity}
                            {--days=7 : Number of days to look back for recent activity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run AML checks on users for suspicious activity';

    protected $amlService;

    /**
     * Create a new command instance.
     */
    public function __construct(AmlService $amlService)
    {
        parent::__construct();
        $this->amlService = $amlService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user');
        $recent = $this->option('recent');
        $days = (int) $this->option('days');

        $this->info('Starting AML checks...');

        try {
            if ($userId) {
                $this->checkSpecificUser($userId);
            } elseif ($recent) {
                $this->checkRecentUsers($days);
            } else {
                $this->checkAllUsers();
            }

            $this->info('AML checks completed successfully.');
            return 0;

        } catch (\Exception $e) {
            $this->error("AML checks failed: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Check a specific user
     */
    protected function checkSpecificUser(int $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return;
        }

        $this->info("Checking user: {$user->name} ({$user->email})");
        $this->runAmlChecksForUser($user);
    }

    /**
     * Check users with recent activity
     */
    protected function checkRecentUsers(int $days)
    {
        $cutoffDate = now()->subDays($days);
        
        $users = User::whereHas('transactions', function ($query) use ($cutoffDate) {
            $query->where('created_at', '>=', $cutoffDate);
        })->get();

        $this->info("Found {$users->count()} users with recent activity (last {$days} days)");

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            $this->runAmlChecksForUser($user);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * Check all users
     */
    protected function checkAllUsers()
    {
        $users = User::all();
        
        $this->info("Checking all {$users->count()} users...");

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            $this->runAmlChecksForUser($user);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * Run AML checks for a specific user
     */
    protected function runAmlChecksForUser(User $user)
    {
        try {
            $flags = $this->amlService->runAmlChecks($user);
            
            if (!empty($flags)) {
                $this->warn("Found " . count($flags) . " AML flags for user {$user->name} ({$user->email})");
                
                foreach ($flags as $flag) {
                    $this->line("  - {$flag->flag_type}: {$flag->description}");
                }
            }

        } catch (\Exception $e) {
            $this->error("Failed to check user {$user->name}: {$e->getMessage()}");
        }
    }
}