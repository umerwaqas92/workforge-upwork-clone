<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'raised_by',
        'reason',
        'description',
        'evidence',
        'status', // opened, under_review, resolved, closed
        'resolution_note',
        'refund_amount',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'evidence' => 'array',
            'refund_amount' => 'decimal:2',
            'resolved_at' => 'datetime',
        ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'raised_by');
    }
}
