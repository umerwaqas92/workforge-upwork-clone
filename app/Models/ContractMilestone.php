<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'title',
        'description',
        'amount',
        'due_date',
        'status', // pending, funded_in_escrow, submitted_for_approval, approved_and_released, cancelled
        'submission_notes',
        'submission_attachments',
        'funded_at',
        'submitted_at',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'submission_attachments' => 'array',
            'funded_at' => 'datetime',
            'submitted_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
