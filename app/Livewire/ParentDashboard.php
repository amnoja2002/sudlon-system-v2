<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use App\Models\ParentAccessRequest;
use App\Models\ParentSubjectAccess;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Schema;

#[Layout('layouts.app')]
class ParentDashboard extends Component
{
    public $selectedStudent = null;
    public $viewingGrades = false;
    public $viewingAttendance = false;
    public $students;

    // New UI state
    public $filterGradeLevel = '';
    public $selectedClassroomId = null;
    public $selectedTeacherId = null;
    public $selectedSubjectId = null;
    public $showingRequestModal = false;
    public $requestReason = '';
    public $studentShortcuts = [];
    public $showRequests = true;
    
    public function mount()
    {
        // In a real app, you'd get the students linked to the parent
        // For now, load all student records
        $this->students = Student::query()
            ->when(Schema::hasColumn('students', 'is_active'), function($q) {
                $q->where('is_active', true);
            })
            ->orderBy('name')
            ->get();
        
        if ($this->students->isNotEmpty()) {
            $this->selectedStudent = $this->students->first()->id;
        }

        // Initialize shortcuts list
        $this->studentShortcuts = session()->get('parent_student_shortcuts', []);
    }

    public function updatedSelectedStudent()
    {
        $this->viewingGrades = false;
        $this->viewingAttendance = false;
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

    public function render()
    {
        return view('livewire.parent-dashboard');
    }

    // Grade levels available based on active classrooms
    public function getGradeLevelsProperty()
    {
        return Classroom::query()
            ->where('is_active', true)
            ->select('grade_level')
            ->distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level');
    }

    // Classrooms list for chosen grade level
    public function getClassroomsProperty()
    {
        return Classroom::query()
            ->where('is_active', true)
            ->when($this->filterGradeLevel, fn($q) => $q->where('grade_level', $this->filterGradeLevel))
            ->orderBy('section')
            ->get();
    }

    public function selectClassroom($classroomId)
    {
        $this->selectedClassroomId = $classroomId;
        $this->selectedTeacherId = null;
        $this->selectedSubjectId = null;
    }

    // Teachers available in classroom (homeroom + subject teachers)
    public function getTeachersForClassroomProperty()
    {
        if (!$this->selectedClassroomId) return collect();
        $classroom = Classroom::with('teacher')->find($this->selectedClassroomId);
        if (!$classroom) return collect();

        $subjectTeachers = Subject::where('classroom_id', $this->selectedClassroomId)
            ->where('is_active', true)
            ->with('teacher')
            ->get()
            ->pluck('teacher')
            ->filter();

        $teachers = collect();
        if ($classroom->teacher) {
            $teachers->push($classroom->teacher);
        }
        $teachers = $teachers->merge($subjectTeachers)->unique('id');
        return $teachers;
    }

    public function selectTeacher($teacherId)
    {
        $this->selectedTeacherId = $teacherId;
        $this->selectedSubjectId = null;
    }

    // Subjects handled by selected teacher within the classroom
    public function getTeacherSubjectsProperty()
    {
        if (!$this->selectedClassroomId || !$this->selectedTeacherId) return collect();
        return Subject::where('classroom_id', $this->selectedClassroomId)
            ->where('teacher_id', $this->selectedTeacherId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function openRequestModal()
    {
        $this->requestReason = '';
        $this->showingRequestModal = true;
    }

    public function requestAllTeachers()
    {
        if (!$this->selectedClassroomId) {
            session()->flash('error', 'Select a classroom first.');
            return;
        }
        $teacherIds = Subject::where('classroom_id', $this->selectedClassroomId)
            ->where('is_active', true)
            ->pluck('teacher_id')->unique();
        foreach ($teacherIds as $tid) {
            ParentAccessRequest::firstOrCreate([
                'parent_id' => auth()->id(),
                'teacher_id' => $tid,
                'classroom_id' => $this->selectedClassroomId,
                'subject_id' => null,
                'status' => 'pending',
            ], [
                'reason' => $this->requestReason,
            ]);
        }
        session()->flash('message', 'Requests submitted to all teachers in this classroom.');
    }

    public function submitAccessRequest($subjectId = null)
    {
        if (!$this->selectedClassroomId || !$this->selectedTeacherId) {
            session()->flash('error', 'Please select a classroom and teacher.');
            return;
        }

        $existing = ParentAccessRequest::where('parent_id', auth()->id())
            ->where('teacher_id', $this->selectedTeacherId)
            ->where('classroom_id', $this->selectedClassroomId)
            ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
            ->whereIn('status', ['pending'])
            ->first();
        if ($existing) {
            session()->flash('message', 'You already have a pending request.');
            $this->showingRequestModal = false;
            return;
        }

        ParentAccessRequest::create([
            'parent_id' => auth()->id(),
            'teacher_id' => $this->selectedTeacherId,
            'classroom_id' => $this->selectedClassroomId,
            'subject_id' => $subjectId,
            'status' => 'pending',
            'reason' => $this->requestReason,
        ]);

        $this->showingRequestModal = false;
        session()->flash('message', 'Access request submitted. Waiting for teacher approval.');
    }

    // Approved subjects for the parent
    public function getApprovedSubjectsProperty()
    {
        return ParentSubjectAccess::with(['classroom', 'subject'])
            ->where('parent_id', auth()->id())
            ->get();
    }

    // Pending/recent decisions for notifications
    public function getAccessRequestsProperty()
    {
        return ParentAccessRequest::with(['teacher', 'classroom', 'subject'])
            ->where('parent_id', auth()->id())
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
    }

    // Students list when a subject is selected
    public function selectSubject($subjectId)
    {
        $this->selectedSubjectId = $subjectId;
    }

    public function getStudentsForSelectedSubjectProperty()
    {
        if (!$this->selectedClassroomId) return collect();
        // Only show students if parent has approved access for this subject/classroom
        $hasAccess = ParentSubjectAccess::where('parent_id', auth()->id())
            ->where('classroom_id', $this->selectedClassroomId)
            ->when($this->selectedSubjectId, fn($q) => $q->where('subject_id', $this->selectedSubjectId))
            ->exists();
        if (!$hasAccess) return collect();
        $classroom = Classroom::with('students')->find($this->selectedClassroomId);
        return $classroom ? $classroom->students : collect();
    }

    public function addStudentShortcut($studentId)
    {
        // Convert to access request for specific student under selected subject
        if (!$this->selectedClassroomId || !$this->selectedTeacherId || !$this->selectedSubjectId) {
            session()->flash('error', 'Select classroom, teacher, and subject before requesting student access.');
            return;
        }
        $exists = ParentAccessRequest::where('parent_id', auth()->id())
            ->where('teacher_id', $this->selectedTeacherId)
            ->where('classroom_id', $this->selectedClassroomId)
            ->where('subject_id', $this->selectedSubjectId)
            ->where('student_id', $studentId)
            ->where('status', 'pending')
            ->exists();
        if ($exists) {
            session()->flash('message', 'You already requested this student.');
            return;
        }
        ParentAccessRequest::create([
            'parent_id' => auth()->id(),
            'teacher_id' => $this->selectedTeacherId,
            'classroom_id' => $this->selectedClassroomId,
            'subject_id' => $this->selectedSubjectId,
            'student_id' => $studentId,
            'status' => 'pending',
            'reason' => 'Request access to student data',
        ]);
        session()->flash('message', 'Request sent to teacher for approval.');
    }

    public function removeStudentShortcut($studentId)
    {
        $this->studentShortcuts = collect($this->studentShortcuts)
            ->reject(fn($id) => (int)$id === (int)$studentId)
            ->values()->all();
        session()->put('parent_student_shortcuts', $this->studentShortcuts);
    }
}