<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'classroom_id',
        'school_year',
        'semester',
        'grades',
        'average',
        'remarks',
        'teacher_comments',
        'generated_by',
        'scope',
        'generated_at',
        'is_active',
    ];

    protected $casts = [
        'grades' => 'array',
        'average' => 'decimal:2',
        'generated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
