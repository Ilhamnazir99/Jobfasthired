<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Skill;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_image', // ✅ Added to support profile photo uploads
        'phone_number',
        'address',
        'company_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile_image' => 'string', // Optional: explicitly cast image path
    ];

    /**
     * Student has many applications (One-to-Many Relationship)
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'student_id');
    }

    /**
     * Student has many skills (Many-to-Many Relationship)
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'student_skills')
                    ->withTimestamps(); // Add timestamps for pivot table
    }

    /**
     * Check if a student has a specific skill
     */
    public function hasSkill($skillName)
    {
        return $this->skills()->where('name', $skillName)->exists();
    }

    /**
     * Attach a skill to the user (Add skill)
     */
    public function addSkill($skillName)
    {
        $skill = Skill::firstOrCreate(['name' => $skillName]);

        // Prevent duplicate attach
        if (!$this->skills->contains($skill->id)) {
            $this->skills()->attach($skill);
        }
    }

    /**
     * Detach a skill from the user (Remove skill)
     */
    public function removeSkill($skillName)
    {
        $skill = Skill::where('name', $skillName)->first();
        if ($skill) {
            $this->skills()->detach($skill);
        }
    }

    
public function employer()
{
    return $this->belongsTo(User::class, 'employer_id');
}

}
