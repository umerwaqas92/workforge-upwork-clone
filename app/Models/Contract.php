<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'proposal_id',
        'client_id',
        'freelancer_id',
        'title',
        'type', // fixed_price, hourly
        'amount',
        'platform_fee_percent',
        'status', // active, completed, cancelled, disputed, paused
        'terms',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'platform_fee_percent' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function milestones()
    {
        return $this->hasMany(ContractMilestone::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function dispute()
    {
        return $this->hasOne(Dispute::class);
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->milestones()->where('status', 'approved_and_released')->sum('amount');
    }

    public function getTotalEscrowAttribute(): float
    {
        return (float) $this->milestones()->whereIn('status', ['funded_in_escrow', 'submitted_for_approval'])->sum('amount');
    }
}
