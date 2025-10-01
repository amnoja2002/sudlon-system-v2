<?php

namespace App\Livewire\Roles\Parent;

use App\Models\Student;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $selectedClassroom = null;
    public $selectedStudent = null;
    public $selectedSubject = null;
    public $viewMode = 'overview'; // overview, subjects, attendance, grades
    public $students = [];
    public $classrooms = [];
    public $currentGradePage = 1;
    public $gradesPerPage = 1; // Show one subject per page on mobile
    
    public function mount(): void
    {
        // Clear any existing session data to avoid duplicates
        session()->forget('matching_students');
        
        // If we have a logged-in user, find students for this user
        if (auth()->check()) {
            $this->students = $this->findMatchingStudents(auth()->user());
            $this->students = $this->normalizeStudents($this->students);
            
            // Remove duplicates by student ID before storing in session
            $this->students = collect($this->students)->unique('id')->values()->toArray();
            
            session(['matching_students' => $this->students]);
        } else {
            $this->students = [];
        }
        
        // If still no students found, redirect to no-match page
        if (empty($this->students)) {
            session()->forget('matching_students'); // Clear the session
            $this->redirect(route('auth.no-match'), navigate: true);
        }
        
        // Group students by classroom
        $this->classrooms = $this->groupStudentsByClassroom($this->students);
    }

    public function updatedSelectedStudent($value): void
    {
        if (is_array($value)) {
            return; // Already normalized
        }
        if ($value instanceof Student) {
            $this->selectedStudent = $value->toArray();
            return;
        }
        if (is_int($value) || (is_string($value) && ctype_digit($value))) {
            $model = Student::find((int) $value);
            $this->selectedStudent = $model ? $model->toArray() : null;
            return;
        }
        $this->selectedStudent = null;
    }

    public function selectClassroom($classroomId): void
    {
        $this->selectedClassroom = $classroomId;
        $this->selectedStudent = null;
        $this->viewMode = 'overview';
        $this->selectedSubject = null;
    }

    public function selectStudent($studentId): void
    {
        $matched = collect($this->students)->firstWhere('id', (int) $studentId);
        if ($matched) {
            $this->selectedStudent = $matched;
            $this->viewMode = 'overview';
            $this->selectedSubject = null;
            return;
        }
        $model = Student::find((int) $studentId);
        $this->selectedStudent = $model ? $model->toArray() : null;
        $this->viewMode = 'overview';
        $this->selectedSubject = null;
    }

    public function setViewMode($mode): void
    {
        $this->viewMode = $mode;
        $this->selectedSubject = null;
        $this->currentGradePage = 1; // Reset pagination when changing view mode
    }

    public function selectSubject($subjectId): void
    {
        $this->selectedSubject = $subjectId;
    }

    public function nextGradePage(): void
    {
        $this->currentGradePage++;
    }

    public function previousGradePage(): void
    {
        if ($this->currentGradePage > 1) {
            $this->currentGradePage--;
        }
    }

    public function getStudentProperty()
    {
        if ($this->selectedStudent) {
            return Student::with(['grades', 'attendance'])->find($this->selectedStudent);
        }
        return null;
    }

    public function getGradesProperty()
    {
        if ($this->student) {
            return $this->student->grades;
        }
        return collect();
    }

    public function getAttendanceProperty()
    {
        if ($this->student) {
            return $this->student->attendance()->latest()->take(30)->get();
        }
        return collect();
    }

    public function getStudentGrades($studentId)
    {
        $student = Student::find((int) $studentId);
        if (!$student) return collect();
        
        // Get grades with subject and teacher information
        return $student->grades()
            ->with(['subject.teacher' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->get();
    }

    public function getStudentAttendance($studentId)
    {
        $student = Student::find((int) $studentId);
        if (!$student) return collect();
        
        // Get attendance records with subject and teacher information
        return $student->attendance()
            ->with(['subject.teacher' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->latest()
            ->take(10)
            ->get();
    }

    public function getStudentSubjects($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) return collect();
        
        // Get subjects for the student's classroom with teacher information
        return \App\Models\Subject::where('classroom_id', $student->classroom_id)
            ->with(['teacher' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->where('is_active', true)
            ->get();
    }

    public function getSubjectAttendance($studentId, $subjectId)
    {
        $student = Student::find($studentId);
        if (!$student) return collect();
        
        // Get attendance records for specific subject with teacher information
        return $student->attendance()
            ->where('subject_id', $subjectId)
            ->with(['subject.teacher' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->latest()
            ->get();
    }

    public function getSubjectGrades($studentId, $subjectId)
    {
        $student = Student::find($studentId);
        if (!$student) return collect();
        
        // Get grades for specific subject with teacher information
        return $student->grades()
            ->where('subject_id', $subjectId)
            ->with(['subject.teacher' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->get();
    }

    public function groupStudentsByClassroom($students): array
    {
        $classrooms = [];
        
        foreach ($students as $student) {
            $classroomId = $student['classroom_id'] ?? null;
            if (!$classroomId) continue;
            
            $classroom = \App\Models\Classroom::find($classroomId);
            if (!$classroom) continue;
            
            // Create a unique key based on grade_level and section
            $classroomKey = $classroom->grade_level . '-' . $classroom->section;
            
            if (!isset($classrooms[$classroomKey])) {
                // Get all subjects for this grade and section (from all classroom IDs with same grade/section)
                $allClassroomIds = \App\Models\Classroom::where('grade_level', $classroom->grade_level)
                    ->where('section', $classroom->section)
                    ->pluck('id');
                
                $subjects = \App\Models\Subject::whereIn('classroom_id', $allClassroomIds)
                    ->with('teacher')
                    ->where('is_active', true)
                    ->get();
                
                // Get unique teachers from subjects
                $teachers = $subjects->pluck('teacher')->unique('id')->filter();
                
                $classrooms[$classroomKey] = [
                    'id' => $classroom->id, // Use the first classroom ID as reference
                    'grade_level' => $classroom->grade_level,
                    'section' => $classroom->section,
                    'display_name' => $classroom->display_name,
                    'teachers' => $teachers,
                    'subjects' => $subjects,
                    'students' => []
                ];
            }
            
            // Add student to the classroom group
            $classrooms[$classroomKey]['students'][] = $student;
        }
        
        return array_values($classrooms);
    }

    public function getClassroomSubjects($classroomId)
    {
        // Find the classroom to get grade and section
        $classroom = \App\Models\Classroom::find($classroomId);
        if (!$classroom) return collect();
        
        // Get all classroom IDs with the same grade and section
        $allClassroomIds = \App\Models\Classroom::where('grade_level', $classroom->grade_level)
            ->where('section', $classroom->section)
            ->pluck('id');
        
        return \App\Models\Subject::whereIn('classroom_id', $allClassroomIds)
            ->with(['teacher' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->where('is_active', true)
            ->get();
    }

    public function getClassroomStudents($classroomId)
    {
        // Find the classroom to get grade and section
        $classroom = \App\Models\Classroom::find($classroomId);
        if (!$classroom) return [];
        
        // Find the classroom group by grade and section
        $classroomKey = $classroom->grade_level . '-' . $classroom->section;
        return collect($this->classrooms)
            ->first(function($classroomData) use ($classroomKey) {
                return ($classroomData['grade_level'] . '-' . $classroomData['section']) === $classroomKey;
            })['students'] ?? [];
    }

    public function getClassroomAttendance($classroomId)
    {
        $studentIds = collect($this->getClassroomStudents($classroomId))->pluck('id');
        
        return \App\Models\Attendance::whereIn('student_id', $studentIds)
            ->with(['student', 'subject.teacher' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->latest()
            ->take(20)
            ->get();
    }

    public function getClassroomGrades($classroomId)
    {
        $studentIds = collect($this->getClassroomStudents($classroomId))->pluck('id');
        
        return \App\Models\Grade::whereIn('student_id', $studentIds)
            ->with(['student', 'subject.teacher' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->get();
    }

    private function findMatchingStudents($user): array
    {
        // First, try to match by exact email
        $studentsByEmail = Student::where(function($query) use ($user) {
            $query->where('mother_email', $user->email)
                  ->orWhere('father_email', $user->email)
                  ->orWhere('guardian_email', $user->email);
        })->get();

        if ($studentsByEmail->count() > 0) {
            return $studentsByEmail->toArray();
        }

        // If no email match, try name matching with more strict criteria
        $nameParts = explode(' ', trim($user->name));
        if (count($nameParts) >= 2) {
            $firstName = trim($nameParts[0]);
            $lastName = trim($nameParts[1]);
            
            // Only proceed if we have valid first and last names
            if (strlen($firstName) >= 2 && strlen($lastName) >= 2) {
                $studentsByName = Student::where(function($query) use ($firstName, $lastName) {
                    $query->where(function($subQuery) use ($firstName, $lastName) {
                        // Check mother's name
                        $subQuery->where('mother_first_name', 'LIKE', "%{$firstName}%")
                                 ->where('mother_last_name', 'LIKE', "%{$lastName}%");
                    })->orWhere(function($subQuery) use ($firstName, $lastName) {
                        // Check father's name
                        $subQuery->where('father_first_name', 'LIKE', "%{$firstName}%")
                                 ->where('father_last_name', 'LIKE', "%{$lastName}%");
                    })->orWhere(function($subQuery) use ($firstName, $lastName) {
                        // Check guardian's name
                        $subQuery->where('guardian_first_name', 'LIKE', "%{$firstName}%")
                                 ->where('guardian_last_name', 'LIKE', "%{$lastName}%");
                    });
                })->get();

                return $studentsByName->toArray();
            }
        }

        // No matches found
        return [];
    }
    
    private function normalizeStudents($input): array
    {
        if (empty($input)) {
            return [];
        }
        if ($input instanceof \Illuminate\Support\Collection) {
            $input = $input->all();
        }
        $allScalars = is_array($input) && collect($input)->every(fn($item) => is_int($item) || (is_string($item) && ctype_digit($item)));
        if ($allScalars) {
            return Student::whereIn('id', collect($input)->map(fn($v) => (int) $v))->get()->toArray();
        }
        $containsModels = is_array($input) && collect($input)->contains(fn($item) => $item instanceof Student);
        if ($containsModels) {
            return collect($input)->map(function ($item) {
                return $item instanceof Student ? $item->toArray() : (array) $item;
            })->values()->all();
        }
        return collect($input)->map(function ($item) {
            if (is_array($item)) {
                return $item;
            }
            if (is_object($item)) {
                return (array) $item;
            }
            if (is_int($item) || (is_string($item) && ctype_digit($item))) {
                $model = Student::find((int) $item);
                return $model ? $model->toArray() : [];
            }
            return [];
        })->filter()->values()->all();
    }

    public function render()
    {
        return view('livewire.roles.parent.dashboard');
    }
}