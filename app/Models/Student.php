<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        // 'email' dropped
        'mother_first_name','mother_last_name','mother_email','mother_contact',
        'father_first_name','father_last_name','father_email','father_contact',
        'guardian_email',
        'grade_level',
        'section',
        'classroom_id',
        'is_active',
        'guardian_first_name',
        'guardian_last_name',
        'guardian_contact',
        'guardian_email',
        'mother_enabled','mother_first_name','mother_last_name','mother_email','mother_contact',
        'father_enabled','father_first_name','father_last_name','father_email','father_contact',
        'guardian_enabled','guardian_first_name','guardian_last_name','guardian_email','guardian_contact',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mother_enabled' => 'boolean',
        'father_enabled' => 'boolean',
        'guardian_enabled' => 'boolean',
    ];

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'classroom_id', 'classroom_id');
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }
}
