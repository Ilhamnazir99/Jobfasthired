<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StudentSkill extends Pivot
{
    protected $table = 'student_skills';

    protected $fillable = [
        'user_id',
        'skill_id',
    ];
}
