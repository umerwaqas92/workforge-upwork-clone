<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'bio',
        'hourly_rate',
        'experience_level',
        'availability',
        'english_level',
        'job_success_score',
        'total_earnings',
        'completed_jobs_count',
        'total_hours_worked',
        'github_url',
        'linkedin_url',
        'portfolio_url',
        'portfolio_items',
        'certifications',
        'education',
        'employment_history',
        'languages',
        'is_top_rated',
        'badge_tier',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'total_earnings' => 'decimal:2',
            'total_hours_worked' => 'decimal:2',
            'portfolio_items' => 'array',
            'certifications' => 'array',
            'education' => 'array',
            'employment_history' => 'array',
            'languages' => 'array',
            'is_top_rated' => 'boolean',
            'badge_tier' => 'string',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recalculateBadgeStatus(): array
    {
        $user = $this->user;
        if (!$user) {
            return ['status' => 'no_user'];
        }

        // 1. Completed contracts
        $completedContracts = Contract::where('freelancer_id', $user->id)
            ->where('status', 'completed')
            ->get();

        $completedCount = $completedContracts->count();
        $earningsFromContracts = $completedContracts->sum('amount');

        $actualEarnings = max((float)$this->total_earnings, (float)$earningsFromContracts);
        $actualCompleted = max((int)$this->completed_jobs_count, $completedCount);

        // 2. JSS Score from client reviews
        $reviews = Review::where('reviewee_id', $user->id)
            ->where('role', 'client_to_freelancer')
            ->get();

        if ($reviews->count() > 0) {
            $avgRating = $reviews->avg('rating');
            $calculatedJss = (int) round(($avgRating / 5.0) * 100);
        } else {
            $calculatedJss = $this->job_success_score ?? 100;
        }

        $completeness = $this->completeness_percentage;

        // 3. Evaluate Badge Tier
        $badgeTier = 'none';
        $isTopRated = false;

        if ($actualEarnings >= 10000 && $calculatedJss >= 90 && $actualCompleted >= 3) {
            $badgeTier = 'top_rated_plus';
            $isTopRated = true;
        } elseif ($actualEarnings >= 1000 && $calculatedJss >= 90 && $actualCompleted >= 3) {
            $badgeTier = 'top_rated';
            $isTopRated = true;
        } elseif ($completeness >= 100 && $calculatedJss >= 80) {
            $badgeTier = 'rising_talent';
            $isTopRated = false;
        }

        $this->update([
            'completed_jobs_count' => $actualCompleted,
            'total_earnings' => $actualEarnings,
            'job_success_score' => $calculatedJss,
            'is_top_rated' => $isTopRated,
            'badge_tier' => $badgeTier,
        ]);

        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'jss' => $calculatedJss,
            'earnings' => $actualEarnings,
            'completed' => $actualCompleted,
            'completeness' => $completeness,
            'badge_tier' => $badgeTier,
            'is_top_rated' => $isTopRated,
        ];
    }

    public function getCompletenessPercentageAttribute(): int
    {
        $score = 0;

        if (!empty($this->title) && strlen($this->title) > 5) {
            $score += 15;
        }

        if (!empty($this->bio) && strlen($this->bio) > 30) {
            $score += 20;
        }

        if ($this->hourly_rate > 0) {
            $score += 10;
        }

        if ($this->user && $this->user->skills()->count() >= 3) {
            $score += 15;
        }

        if (!empty($this->portfolio_items) && count($this->portfolio_items) > 0) {
            $score += 15;
        }

        if (!empty($this->employment_history) && count($this->employment_history) > 0) {
            $score += 15;
        }

        if ((!empty($this->education) && count($this->education) > 0) || (!empty($this->certifications) && count($this->certifications) > 0)) {
            $score += 10;
        }

        return min(100, $score);
    }

    public function getMissingProfileStepsAttribute(): array
    {
        $missing = [];

        if (empty($this->title) || strlen($this->title) <= 5) {
            $missing[] = ['step' => 'Professional Title', 'weight' => '+15%', 'action' => 'Add your specialized job title'];
        }

        if (empty($this->bio) || strlen($this->bio) <= 30) {
            $missing[] = ['step' => 'Overview Bio', 'weight' => '+20%', 'action' => 'Write an engaging professional bio'];
        }

        if (!$this->user || $this->user->skills()->count() < 3) {
            $missing[] = ['step' => 'Skills List', 'weight' => '+15%', 'action' => 'Select at least 3 relevant skills'];
        }

        if (empty($this->portfolio_items) || count($this->portfolio_items) === 0) {
            $missing[] = ['step' => 'Portfolio Projects', 'weight' => '+15%', 'action' => 'Add your past project work & case studies'];
        }

        if (empty($this->employment_history) || count($this->employment_history) === 0) {
            $missing[] = ['step' => 'Employment History', 'weight' => '+15%', 'action' => 'Add past companies and work roles'];
        }

        if ((empty($this->education) || count($this->education) === 0) && (empty($this->certifications) || count($this->certifications) === 0)) {
            $missing[] = ['step' => 'Education & Certifications', 'weight' => '+10%', 'action' => 'Add your degree or certifications'];
        }

        return $missing;
    }
}
