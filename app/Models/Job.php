<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'location',
        'salary',
        'schedule',
        'category',
        'latitude',
        'longitude',
        'address', // ✅ FIXED this from 'full_address'
    ];

    public function employer()
{
    return $this->belongsTo(User::class, 'employer_id')->where('role', 'employer');
}


    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skill');
    }
    
}
