<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'job_id',
        'status',
        'message',
    ];

    // Relationship: Application belongs to a Student (User)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship: Application belongs to a Job
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
