<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\ReportCard;

class StudentManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a teacher to assign to classrooms
        $teacher = \App\Models\User::whereHas('role', function($q) {
            $q->where('slug', 'teacher');
        })->first();

        // Create sample classrooms
        $classrooms = [
            ['name' => 'Grade 1 - A', 'grade_level' => 1, 'section' => 'A', 'description' => 'Grade 1 Section A'],
            ['name' => 'Grade 1 - B', 'grade_level' => 1, 'section' => 'B', 'description' => 'Grade 1 Section B'],
            ['name' => 'Grade 2 - A', 'grade_level' => 2, 'section' => 'A', 'description' => 'Grade 2 Section A'],
            ['name' => 'Grade 3 - A', 'grade_level' => 3, 'section' => 'A', 'description' => 'Grade 3 Section A'],
        ];

        foreach ($classrooms as $classroomData) {
            Classroom::firstOrCreate(
                ['name' => $classroomData['name']],
                array_merge($classroomData, [
                    'is_active' => true, 
                    'max_students' => 30,
                    'teacher_id' => $teacher?->id
                ])
            );
        }

        // Create sample subjects for each classroom
        $subjects = [
            ['name' => 'Mathematics', 'description' => 'Basic Mathematics'],
            ['name' => 'English', 'description' => 'English Language'],
            ['name' => 'Science', 'description' => 'General Science'],
            ['name' => 'Filipino', 'description' => 'Filipino Language'],
            ['name' => 'Araling Panlipunan', 'description' => 'Social Studies'],
            ['name' => 'MAPEH', 'description' => 'Music, Arts, PE, Health'],
        ];

        // Get all classrooms to assign subjects to each one
        $allClassrooms = Classroom::all();

        foreach ($allClassrooms as $classroom) {
            foreach ($subjects as $subjectData) {
                Subject::firstOrCreate(
                    [
                        'name' => $subjectData['name'],
                        'classroom_id' => $classroom->id
                    ],
                    array_merge($subjectData, [
                        'is_active' => true,
                        'teacher_id' => $teacher?->id,
                        'classroom_id' => $classroom->id
                    ])
                );
            }
        }

        // Create sample students
        $students = [
            ['name' => 'Juan Dela Cruz', 'email' => 'juan.delacruz@student.com', 'grade_level' => 1, 'section' => 'A'],
            ['name' => 'Maria Santos', 'email' => 'maria.santos@student.com', 'grade_level' => 1, 'section' => 'A'],
            ['name' => 'Pedro Garcia', 'email' => 'pedro.garcia@student.com', 'grade_level' => 1, 'section' => 'B'],
            ['name' => 'Ana Rodriguez', 'email' => 'ana.rodriguez@student.com', 'grade_level' => 2, 'section' => 'A'],
            ['name' => 'Luis Martinez', 'email' => 'luis.martinez@student.com', 'grade_level' => 2, 'section' => 'A'],
            ['name' => 'Carmen Lopez', 'email' => 'carmen.lopez@student.com', 'grade_level' => 3, 'section' => 'A'],
        ];

        $classrooms = Classroom::all();

        foreach ($students as $studentData) {
            $classroom = $classrooms->where('grade_level', $studentData['grade_level'])
                                  ->where('section', $studentData['section'])
                                  ->first();

            $student = Student::firstOrCreate(
                ['email' => $studentData['email']],
                array_merge($studentData, [
                    'classroom_id' => $classroom?->id,
                    'is_active' => true
                ])
            );

            // Get subjects specific to the student's classroom
            $classroomSubjects = Subject::where('classroom_id', $classroom?->id)->get();

            // Create sample grades for each student
            $terms = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
            foreach ($classroomSubjects->take(4) as $subject) {
                foreach ($terms as $term) {
                    Grade::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'term' => $term
                        ],
                        [
                            'subject' => $subject->name,
                            'score' => rand(75, 98),
                            'is_active' => true
                        ]
                    );
                }
            }

            // Create sample report card
            if ($student->grades()->count() > 0) {
                $average = $student->grades()->avg('score');
                $remarks = $average >= 90 ? 'Excellent' : 
                          ($average >= 85 ? 'Very Good' : 
                          ($average >= 80 ? 'Good' : 'Fair'));

                ReportCard::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'school_year' => '2024-2025',
                        'semester' => '1st'
                    ],
                    [
                        'classroom_id' => $classroom?->id,
                        'grades' => $student->grades()->get()->groupBy('subject')->map(function($grades) {
                            return $grades->pluck('score', 'term')->toArray();
                        })->toArray(),
                        'average' => round($average, 2),
                        'remarks' => $remarks,
                        'teacher_comments' => 'Student shows good progress in all subjects. Keep up the good work!',
                        'generated_by' => 1, // Assuming user ID 1 exists
                        'generated_at' => now(),
                        'is_active' => true
                    ]
                );
            }
        }
    }
}
