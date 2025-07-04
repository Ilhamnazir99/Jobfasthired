<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
