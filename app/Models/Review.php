<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'reviewer_id',
        'reviewee_id',
        'role', // client_to_freelancer, freelancer_to_client
        'rating',
        'communication_rating',
        'quality_rating',
        'deadline_rating',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:2',
            'communication_rating' => 'decimal:2',
            'quality_rating' => 'decimal:2',
            'deadline_rating' => 'decimal:2',
        ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}
