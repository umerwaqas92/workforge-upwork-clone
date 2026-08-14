<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobPosting extends Model
{
    use HasFactory;

    protected $table = 'job_postings';

    protected $fillable = [
        'client_id',
        'category_id',
        'title',
        'slug',
        'description',
        'type', // fixed_price, hourly
        'budget_min',
        'budget_max',
        'hourly_rate_min',
        'hourly_rate_max',
        'experience_level', // entry, intermediate, expert
        'duration', // less_than_1_month, 1_to_3_months, 3_to_6_months, more_than_6_months
        'weekly_hours', // less_than_30, more_than_30, none
        'status', // draft, open, in_progress, completed, closed
        'proposals_count',
        'hires_count',
        'attachments',
        'is_featured',
        'is_urgent',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'hourly_rate_min' => 'decimal:2',
            'hourly_rate_max' => 'decimal:2',
            'attachments' => 'array',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title) . '-' . Str::random(6);
            }
            if (empty($job->published_at)) {
                $job->published_at = now();
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_posting_skills')->withTimestamps();
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_jobs', 'job_posting_id', 'user_id')->withTimestamps();
    }

    public function getBudgetFormattedAttribute(): string
    {
        if ($this->type === 'fixed_price') {
            if ($this->budget_max && $this->budget_min && $this->budget_min != $this->budget_max) {
                return '$' . number_format($this->budget_min) . ' - $' . number_format($this->budget_max);
            }
            return '$' . number_format($this->budget_max ?: $this->budget_min ?: 0);
        }

        if ($this->hourly_rate_min && $this->hourly_rate_max) {
            return '$' . number_format($this->hourly_rate_min, 2) . ' - $' . number_format($this->hourly_rate_max, 2) . '/hr';
        }
        return '$' . number_format($this->hourly_rate_min ?: 20, 2) . '/hr';
    }

    public function getFormattedExperienceAttribute(): string
    {
        return match ($this->experience_level) {
            'entry' => 'Entry level ($)',
            'intermediate' => 'Intermediate ($$)',
            'expert' => 'Expert ($$$)',
            default => ucfirst($this->experience_level),
        };
    }
}
