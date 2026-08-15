<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'freelancer_id',
        'bid_amount',
        'hourly_rate',
        'platform_fee',
        'receive_amount',
        'delivery_time_days',
        'cover_letter',
        'milestones',
        'attachments',
        'status', // pending, shortlisted, accepted, rejected, withdrawn
        'client_seen',
        'is_boosted',
        'boosted_connects',
    ];

    protected function casts(): array
    {
        return [
            'bid_amount' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'receive_amount' => 'decimal:2',
            'delivery_time_days' => 'integer',
            'milestones' => 'array',
            'attachments' => 'array',
            'client_seen' => 'boolean',
            'is_boosted' => 'boolean',
            'boosted_connects' => 'integer',
        ];
    }

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }
}
