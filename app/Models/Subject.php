<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'teacher_id',
        'classroom_id',
        'grade_level',
        'section',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Student relation removed: subjects are linked by classroom and teacher

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
