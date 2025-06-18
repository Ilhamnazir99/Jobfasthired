<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Job;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * A skill can belong to many students.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'student_skills');
    }

    /**
     * A skill can be required by many jobs.
     */
    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'job_skill');
    }
}
