<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_website',
        'company_size',
        'industry',
        'tagline',
        'about',
        'payment_verified',
        'total_spent',
        'hires_count',
        'active_contracts_count',
    ];

    protected function casts(): array
    {
        return [
            'payment_verified' => 'boolean',
            'total_spent' => 'decimal:2',
            'hires_count' => 'integer',
            'active_contracts_count' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
