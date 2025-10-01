<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_level',
        'section',
        'description',
        'teacher_id',
        'max_students',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }

    // Derived display name since `name` column was dropped
    public function getDisplayNameAttribute(): string
    {
        $grade = (string)($this->grade_level ?? '');
        $section = trim((string)($this->section ?? ''));
        $parts = [];
        if ($grade !== '') {
            $parts[] = 'Grade ' . $grade;
        }
        if ($section !== '') {
            $parts[] = $section;
        }
        return count($parts) > 0 ? implode(' - ', $parts) : 'Classroom #' . $this->id;
    }
}
