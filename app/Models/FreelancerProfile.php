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
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
