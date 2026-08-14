<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // client, freelancer, admin
        'avatar',
        'phone',
        'status', // active, suspended, pending
        'country',
        'city',
        'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isFreelancer(): bool
    {
        return $this->role === 'freelancer';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function freelancerProfile()
    {
        return $this->hasOne(FreelancerProfile::class);
    }

    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills')->withPivot('proficiency_level')->withTimestamps();
    }

    public function postedJobs()
    {
        return $this->hasMany(JobPosting::class, 'client_id');
    }

    public function savedJobs()
    {
        return $this->belongsToMany(JobPosting::class, 'saved_jobs', 'user_id', 'job_posting_id')->withTimestamps();
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'freelancer_id');
    }

    public function clientContracts()
    {
        return $this->hasMany(Contract::class, 'client_id');
    }

    public function freelancerContracts()
    {
        return $this->hasMany(Contract::class, 'freelancer_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')->withPivot('last_read_at')->withTimestamps();
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return str_starts_with($this->avatar, 'http') ? $this->avatar : asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=108a00&color=ffffff&bold=true';
    }

    public function getRatingAttribute(): float
    {
        return round($this->reviewsReceived()->avg('rating') ?? 5.0, 1);
    }

    public function getRatingCountAttribute(): int
    {
        return $this->reviewsReceived()->count();
    }
}
