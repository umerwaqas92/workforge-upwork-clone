<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSkill extends Pivot
{
    protected $table = 'user_skills';

    protected $fillable = [
        'user_id',
        'skill_id',
        'proficiency_level',
    ];
}
