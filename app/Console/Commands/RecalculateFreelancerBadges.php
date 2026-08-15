<?php

namespace App\Console\Commands;

use App\Models\FreelancerProfile;
use App\Models\User;
use Illuminate\Console\Command;

class RecalculateFreelancerBadges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freelancers:recalculate-badges {--user= : Optional user ID to recalculate specific freelancer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically recalculate Job Success Score (JSS), earnings, completed contracts, and award badge tiers (Rising Talent, Top Rated, Top Rated Plus).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('⭐ Starting Freelancer Reputation & Badge Tier Recalculation Engine...');

        $userId = $this->option('user');

        $query = FreelancerProfile::with('user');
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $profiles = $query->get();

        if ($profiles->isEmpty()) {
            $this->warn('No freelancer profiles found to recalculate.');
            return Command::SUCCESS;
        }

        $tableData = [];
        $promotedCount = 0;

        $bar = $this->output->createProgressBar($profiles->count());
        $bar->start();

        foreach ($profiles as $profile) {
            $result = $profile->recalculateBadgeStatus();
            
            if (isset($result['badge_tier']) && $result['badge_tier'] !== 'none') {
                $promotedCount++;
            }

            $badgeFormatted = match($result['badge_tier'] ?? 'none') {
                'top_rated_plus' => '👑 Top Rated Plus',
                'top_rated' => '⭐ Top Rated',
                'rising_talent' => '🌱 Rising Talent',
                default => '— None'
            };

            $tableData[] = [
                'ID' => $result['user_id'] ?? 'N/A',
                'Freelancer' => $result['name'] ?? 'N/A',
                'JSS' => ($result['jss'] ?? 0) . '%',
                'Completed' => $result['completed'] ?? 0,
                'Earnings' => '$' . number_format($result['earnings'] ?? 0, 2),
                'Profile' => ($result['completeness'] ?? 0) . '%',
                'Assigned Badge' => $badgeFormatted,
            ];

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['User ID', 'Freelancer Name', 'JSS', 'Completed Jobs', 'Total Earnings', 'Profile %', 'Assigned Badge'],
            $tableData
        );

        $this->info("✓ Successfully evaluated {$profiles->count()} freelancers. Active badge holders: {$promotedCount}");

        return Command::SUCCESS;
    }
}
