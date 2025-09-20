<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\ReportCard;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
class TeacherDashboard extends Component
{
    use WithPagination;

    // Current view state
    public $currentView = 'dashboard'; // dashboard, classrooms, students, subjects, grades, attendance, reports, requests, profile, subject-grades
    public $selectedClassroom = null;
    public $selectedStudent = null;
    public $selectedSubject = null;
    public $selectedClassroomForSubjects = null;
    public $selectedClassroomForAttendance = null;
    public $selectedClassroomForReports = null;
    public $selectedSubjectForGrades = null;

    // Modal states
    public $showingClassroomModal = false;
    public $showingStudentModal = false;
    public $showingSubjectModal = false;
    public $showingGradeModal = false;
    public $showingAttendanceModal = false;
    public $showingReportCardModal = false;
    public $showingProfileModal = false;
    public $showingAttendanceForm = false;

    // Requests moderation
    public $showingRejectModal = false;
    public $selectedRequestId = null;
    public $rejectReason = '';

    // Form data
    public $classroomData = [
        'name' => '',
        'grade_level' => '',
        'section' => '',
        'description' => '',
        'max_students' => 40,
    ];

    public $studentData = [
        'name' => '',
        'email' => null,
        'grade_level' => '',
        'section' => '',
        'classroom_id' => null,
    ];

    public $subjectData = [
        'name' => '',
        'description' => '',
        'classroom_id' => null,
        'grade_level' => '',
        'section' => '',
    ];

    public $gradeData = [
        'student_id' => null,
        'subject_id' => null,
        'subject' => '',
        'term' => '',
        'score' => '',
    ];

    public $attendanceData = [
        'student_id' => null,
        'subject_id' => null,
        'status' => 'present',
        'date' => '',
    ];

    public $studentAttendanceData = [];

    public $reportCardData = [
        'student_id' => null,
        'classroom_id' => null,
        'school_year' => '',
        'semester' => '',
        'teacher_comments' => '',
    ];

    public $profileData = [
        'name' => '',
        'email' => '',
    ];

    // Filters
    public $search = '';
    public $filterGradeLevel = '';
    public $filterClassroom = '';
    public $filterStatus = '';

    // Pagination
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->profileData = auth()->user()->only(['name', 'email']);
        $this->attendanceData['date'] = now()->toDateString();
        $this->reportCardData['school_year'] = now()->year . '-' . (now()->year + 1);
    }

    // Navigation methods
    public function setView($view)
    {
        $this->currentView = $view;
        $this->reset(['selectedClassroom', 'selectedStudent', 'selectedSubject', 'selectedClassroomForSubjects', 'selectedClassroomForAttendance', 'selectedClassroomForReports', 'selectedSubjectForGrades']);
        $this->resetPage();
    }

    public function selectClassroom($classroomId)
    {
        $this->selectedClassroom = $classroomId;
        $this->currentView = 'students';
    }

    public function selectStudent($studentId)
    {
        $this->selectedStudent = $studentId;
        $this->currentView = 'student-subjects';
    }

    public function selectSubject($subjectId)
    {
        $this->selectedSubject = $subjectId;
        $this->currentView = 'student-grades';
    }

    public function selectClassroomForSubjects($classroomId)
    {
        $this->selectedClassroomForSubjects = $classroomId;
        $this->currentView = 'subjects';
    }

    public function addSubjectForClassroom($classroomId)
    {
        $this->selectedClassroomForSubjects = $classroomId;
        $this->reset('subjectData');
        $this->subjectData['classroom_id'] = $classroomId;
        $this->selectedSubject = null;
        $this->showingSubjectModal = true;
    }

    public function selectClassroomForAttendance($classroomId)
    {
        $this->selectedClassroomForAttendance = $classroomId;
        $this->currentView = 'attendance';
        $this->selectedSubject = null;
        $this->showingAttendanceForm = false;
    }

    public function selectSubjectForAttendance($subjectId)
    {
        $this->selectedSubject = $subjectId;
        $this->attendanceData['subject_id'] = $subjectId;
        $this->recordNewAttendance();
    }

    public function selectClassroomForReports($classroomId)
    {
        $this->selectedClassroomForReports = $classroomId;
        $this->currentView = 'reports';
    }

    public function selectSubjectForGrades($subjectId)
    {
        $this->selectedSubjectForGrades = $subjectId;
        $this->currentView = 'subject-grades';
    }

    // Classroom Management
    public function showClassroomModal($classroomId = null)
    {
        $this->selectedClassroom = $classroomId ? Classroom::find($classroomId) : null;
        if ($this->selectedClassroom) {
            $this->classroomData = $this->selectedClassroom->only(['name', 'grade_level', 'section', 'description', 'max_students']);
        } else {
            $this->reset('classroomData');
            // Prefill classroom name with current teacher's name
            $this->classroomData['name'] = auth()->user()->name;
        }
        $this->showingClassroomModal = true;
    }

    public function saveClassroom()
    {
        $this->validate([
            'classroomData.name' => 'required|min:3',
            'classroomData.grade_level' => 'required',
            'classroomData.section' => 'required',
            'classroomData.max_students' => 'required|integer|min:1|max:50',
        ]);

        $data = $this->classroomData;
        $data['teacher_id'] = auth()->id();

        // Enforce unique (teacher_id, grade_level, section)
        $exists = Classroom::where('teacher_id', auth()->id())
            ->where('grade_level', $data['grade_level'])
            ->where('section', $data['section'])
            ->when($this->selectedClassroom, fn($q) => $q->where('id', '!=', $this->selectedClassroom->id))
            ->exists();
        if ($exists) {
            session()->flash('error', 'You already have a classroom with this grade level and section.');
            return;
        }

        if ($this->selectedClassroom) {
            $this->selectedClassroom->update($data);
            $message = 'Classroom updated successfully.';
        } else {
            $new = Classroom::create($data);
            // If same grade/section exists globally under another teacher, do not auto-copy
            $message = 'Classroom created successfully.';
        }

        $this->showingClassroomModal = false;
        session()->flash('message', $message);
    }

    public function deleteClassroom($classroomId)
    {
        $classroom = Classroom::find($classroomId);
        if ($classroom && $classroom->teacher_id === auth()->id()) {
            // Soft delete - make classroom inactive
            $classroom->update(['is_active' => false]);
            
            // Make all students in this classroom inactive
            Student::where('classroom_id', $classroomId)->update(['is_active' => false]);
            
            // Make all subjects in this classroom inactive
            Subject::where('classroom_id', $classroomId)->update(['is_active' => false]);
            
            // Make all grades for students in this classroom inactive
            Grade::whereHas('student', function($query) use ($classroomId) {
                $query->where('classroom_id', $classroomId);
            })->update(['is_active' => false]);
            
            // Make all attendance for students in this classroom inactive
            Attendance::whereHas('student', function($query) use ($classroomId) {
                $query->where('classroom_id', $classroomId);
            })->update(['is_active' => false]);
            
            // Make all report cards for this classroom inactive
            ReportCard::where('classroom_id', $classroomId)->update(['is_active' => false]);
            
            session()->flash('message', 'Classroom and all related data have been deactivated successfully.');
        }
    }

    // Student Management
    public function showStudentModal($studentId = null)
    {
        $this->selectedStudent = $studentId ? Student::find($studentId) : null;
        if ($this->selectedStudent) {
            $this->studentData = $this->selectedStudent->only(['name', 'email', 'grade_level', 'section', 'classroom_id']);
        } else {
            $this->reset('studentData');
            $this->studentData['classroom_id'] = $this->selectedClassroom;
            if ($this->selectedClassroom) {
                $classroom = Classroom::find($this->selectedClassroom);
                if ($classroom) {
                    $this->studentData['grade_level'] = $classroom->grade_level;
                    $this->studentData['section'] = $classroom->section;
                }
            }
        }
        $this->showingStudentModal = true;
    }

    public function saveStudent()
    {
        $this->validate([
            'studentData.name' => 'required|min:3',
            'studentData.email' => 'nullable|email|unique:students,email' . ($this->selectedStudent ? ','.$this->selectedStudent->id : ''),
            'studentData.grade_level' => 'required',
            'studentData.section' => 'required',
            'studentData.classroom_id' => 'required|exists:classrooms,id',
        ]);

        // If there is an existing active classroom for the same grade level & section, force using it
        $gradeLevel = $this->studentData['grade_level'] ?? null;
        $section = $this->studentData['section'] ?? null;
        if (!empty($gradeLevel) && !empty($section)) {
            $existingClassroom = Classroom::where('grade_level', $gradeLevel)
                ->where('section', $section)
                ->where('is_active', true)
                ->first();
            if ($existingClassroom) {
                $this->studentData['classroom_id'] = $existingClassroom->id;
            }
        }

        // Prevent duplicate active students in the same grade level & section by name or email
        $duplicateQuery = Student::where('is_active', true)
            ->where('grade_level', $this->studentData['grade_level'])
            ->where('section', $this->studentData['section'])
            ->where(function($q){
                $name = $this->studentData['name'];
                $email = $this->studentData['email'] ?? null;
                $q->where('name', $name);
                if (!empty($email)) {
                    $q->orWhere('email', $email);
                }
            });
        if ($this->selectedStudent) {
            $duplicateQuery->where('id', '!=', $this->selectedStudent->id);
        }
        if ($duplicateQuery->exists()) {
            session()->flash('error', 'An active student with the same name or email already exists in this grade and section.');
            return;
        }

        // Ensure student is active on create/update unless explicitly deactivated elsewhere
        $this->studentData['is_active'] = true;

        if ($this->selectedStudent) {
            $this->selectedStudent->update($this->studentData);
            $message = 'Student updated successfully.';
        } else {
            Student::create($this->studentData);
            $message = 'Student added successfully.';
        }

        $this->showingStudentModal = false;
        session()->flash('message', $message);
    }

    public function backToClassrooms()
    {
        $this->setView('classrooms');
    }

    public function deleteStudent($studentId)
    {
        $student = Student::find($studentId);
        if ($student) {
            $student->delete();
            session()->flash('message', 'Student deleted successfully.');
        }
    }

    // When classroom is changed in the student form, sync grade/section from target classroom
    public function updatedStudentDataClassroomId($classroomId): void
    {
        if (!$classroomId) {
            return;
        }

        $classroom = Classroom::find($classroomId);
        if ($classroom) {
            $this->studentData['grade_level'] = $classroom->grade_level;
            $this->studentData['section'] = $classroom->section;
        }
    }

    // Subject Management
    public function showSubjectModal($subjectId = null)
    {
        $this->selectedSubject = $subjectId ? Subject::find($subjectId) : null;
        if ($this->selectedSubject) {
            $this->subjectData = $this->selectedSubject->only(['name', 'description', 'classroom_id']);
        } else {
            $this->reset('subjectData');
            $this->subjectData['classroom_id'] = $this->selectedClassroomForSubjects;
        }
        $this->showingSubjectModal = true;
    }

    public function saveSubject()
    {
        // If grade/section provided, try to resolve classroom automatically
        $grade = trim((string)($this->subjectData['grade_level'] ?? ''));
        $section = trim((string)($this->subjectData['section'] ?? ''));
        if ($grade !== '' && $section !== '') {
            $existing = Classroom::where('grade_level', $grade)
                ->where('section', $section)
                ->where('is_active', true)
                ->first();
            if ($existing) {
                $this->subjectData['classroom_id'] = $existing->id;
            }
        }

        $this->validate([
            'subjectData.name' => ['required','string','min:2','max:50','regex:/^[A-Za-z0-9\s\-\.\&]+$/'],
            'subjectData.classroom_id' => 'required|exists:classrooms,id',
        ]);

        $data = $this->subjectData;
        $data['teacher_id'] = auth()->id();

        // Prevent creating duplicate subject names within the same classroom (regardless of teacher)
        $conflict = Subject::where('classroom_id', $data['classroom_id'])
            ->where('name', $data['name'])
            ->exists();
        if ($conflict) {
            session()->flash('error', 'This subject already exists in the selected classroom. Choose a different subject.');
            return;
        }

        if ($this->selectedSubject) {
            $this->selectedSubject->update($data);
            $message = 'Subject updated successfully.';
        } else {
            Subject::create($data);
            $message = 'Subject added successfully.';
        }

        $this->showingSubjectModal = false;
        session()->flash('message', $message);
    }

    public function deleteSubject($subjectId)
    {
        $subject = Subject::find($subjectId);
        if ($subject && $subject->teacher_id === auth()->id()) {
            $subject->delete();
            session()->flash('message', 'Subject deleted successfully.');
        }
    }

    // Grade Management
    public function showGradeModal($gradeId = null)
    {
        $grade = $gradeId ? Grade::find($gradeId) : null;
        if ($grade) {
            $this->gradeData = $grade->only(['student_id', 'subject_id', 'subject', 'term', 'score']);
        } else {
            $this->reset('gradeData');
            $this->gradeData['student_id'] = $this->selectedStudent;
            $this->gradeData['subject_id'] = $this->selectedSubject;
        }
        $this->showingGradeModal = true;
    }

    public function saveGrade()
    {
        $this->validate([
            'gradeData.student_id' => 'required|exists:students,id',
            'gradeData.subject' => 'required',
            'gradeData.term' => 'required',
            // enforce two decimals and range 60.00 - 99.99
            'gradeData.score' => [
                'required',
                'regex:/^\d+\.\d{2}$/',
                'numeric',
                'min:60',
                'max:99.99',
            ],
        ]);

        if (isset($this->gradeData['subject_id']) && $this->gradeData['subject_id']) {
            $subject = Subject::find($this->gradeData['subject_id']);
            if ($subject) {
                $this->gradeData['subject'] = $subject->name;
            }
        }

        $grade = Grade::where('student_id', $this->gradeData['student_id'])
                     ->where('subject', $this->gradeData['subject'])
                     ->where('term', $this->gradeData['term'])
                     ->first();

        if ($grade) {
            $grade->update(['score' => $this->gradeData['score']]);
            $message = 'Grade updated successfully.';
        } else {
            Grade::create($this->gradeData);
            $message = 'Grade added successfully.';
        }

        $this->showingGradeModal = false;
        session()->flash('message', $message);
    }

    public function saveStudentGrade($studentId, $score, $term = '1st Quarter', $subjectId = null)
    {
        // Validate score format and range: two decimals and 60.00 - 99.99
        if (!preg_match('/^\d+\.\d{2}$/', (string)$score) || (float)$score < 60 || (float)$score > 99.99) {
            session()->flash('error', 'Score must be between 60.00 and 99.99 with two decimals.');
            return;
        }
        // If subjectId is not provided, try to get it from selectedSubject or find it from the student's subjects
        if (!$subjectId) {
            if ($this->selectedSubject) {
                $subjectId = $this->selectedSubject;
            } else {
                // Find the subject that the student is enrolled in for this teacher
                $subject = Subject::where('teacher_id', auth()->id())
                                 ->whereHas('classroom.students', function($query) use ($studentId) {
                                     $query->where('id', $studentId);
                                 })
                                 ->first();
                if (!$subject) {
                    session()->flash('error', 'Subject not found for this student.');
                    return;
                }
                $subjectId = $subject->id;
            }
        }

        $subject = Subject::find($subjectId);
        if (!$subject) {
            session()->flash('error', 'Subject not found.');
            return;
        }

        $grade = Grade::where('student_id', $studentId)
                     ->where('subject_id', $subjectId)
                     ->where('term', $term)
                     ->first();

        $gradeData = [
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'subject' => $subject->name,
            'term' => $term,
            'score' => number_format((float)$score, 2, '.', ''),
        ];

        if ($grade) {
            $grade->update(['score' => number_format((float)$score, 2, '.', '')]);
            $message = 'Grade updated successfully.';
        } else {
            Grade::create($gradeData);
            $message = 'Grade added successfully.';
        }

        session()->flash('message', $message);
    }

    public function deleteGrade($gradeId)
    {
        $grade = Grade::find($gradeId);
        if ($grade) {
            $grade->delete();
            session()->flash('message', 'Grade deleted successfully.');
        }
    }

    // Attendance Management
    public function showAttendanceModal($studentId = null)
    {
        $this->attendanceData['student_id'] = $studentId;
        $this->showingAttendanceModal = true;
    }


    public function deleteAttendance($attendanceId)
    {
        $attendance = Attendance::find($attendanceId);
        if ($attendance && $attendance->marked_by === auth()->id()) {
            $attendance->delete();
            session()->flash('message', 'Attendance deleted successfully.');
        }
    }

    public function recordNewAttendance()
    {
        if (!$this->selectedClassroomForAttendance) {
            session()->flash('error', 'Please select a classroom.');
            return;
        }
        if (!$this->selectedSubject) {
            session()->flash('error', 'Please select a subject.');
            return;
        }

        $this->showingAttendanceForm = true;
        $this->studentAttendanceData = [];
        
        // Initialize all students as unmarked, but check for existing attendance
        if ($this->selectedClassroomForAttendance) {
            $classroom = Classroom::find($this->selectedClassroomForAttendance);
            if ($classroom) {
                foreach ($classroom->students as $student) {
                    // Check if attendance already exists for this date
                    $existingAttendance = Attendance::where('student_id', $student->id)
                                                   ->where('subject_id', $this->selectedSubject)
                                                   ->whereDate('date', $this->attendanceData['date'])
                                                   ->first();
                    
                    if ($existingAttendance) {
                        // If attendance exists, mark as already recorded
                        $this->studentAttendanceData[$student->id] = 'recorded_' . $existingAttendance->status;
                    } else {
                        // If no attendance exists, mark as unmarked
                        $this->studentAttendanceData[$student->id] = 'unmarked';
                    }
                }
            }
        }
    }

    public function updateStudentAttendance($studentId, $status)
    {
        $this->studentAttendanceData[$studentId] = $status;
    }

    public function deleteExistingAttendance($studentId)
    {
        // Delete existing attendance for this student, subject, and date
        Attendance::where('student_id', $studentId)
                  ->where('subject_id', $this->selectedSubject)
                  ->whereDate('date', $this->attendanceData['date'])
                  ->delete();
        
        // Mark as unmarked so they can be re-marked
        $this->studentAttendanceData[$studentId] = 'unmarked';
        
        session()->flash('message', 'Existing attendance deleted. Student can now be re-marked.');
    }

    public function saveAttendance()
    {
        if (!$this->attendanceData['date']) {
            session()->flash('error', 'Please select a date.');
            return;
        }
        if (!$this->selectedSubject) {
            session()->flash('error', 'Please select a subject.');
            return;
        }

        $savedCount = 0;
        foreach ($this->studentAttendanceData as $studentId => $status) {
            // Only save marked students (not unmarked)
            if ($status === 'unmarked') {
                continue;
            }

            // Check if attendance already exists for this date
            $existing = Attendance::where('student_id', $studentId)
                                 ->where('subject_id', $this->selectedSubject)
                                 ->whereDate('date', $this->attendanceData['date'])
                                 ->first();

            $data = [
            'student_id' => $studentId,
            'subject_id' => $this->selectedSubject,
            'status' => $status,
                'date' => $this->attendanceData['date'],
            'marked_by' => auth()->id(),
            ];

            if ($existing) {
                $existing->update($data);
            } else {
                Attendance::create($data);
            }
            $savedCount++;
        }

        $this->showingAttendanceForm = false;
        $this->studentAttendanceData = [];
        session()->flash('message', "Attendance recorded for {$savedCount} students successfully.");
    }

    public function cancelAttendance()
    {
        $this->showingAttendanceForm = false;
        $this->studentAttendanceData = [];
    }

    // Report Card Management
    public function showReportCardModal($studentId = null)
    {
        $this->reportCardData['student_id'] = $studentId;
        $this->reportCardData['classroom_id'] = $this->selectedClassroom;
        $this->showingReportCardModal = true;
    }

    public function generateReportCard()
    {
        $this->validate([
            'reportCardData.student_id' => 'required|exists:students,id',
            'reportCardData.classroom_id' => 'required|exists:classrooms,id',
            'reportCardData.school_year' => 'required',
            'reportCardData.semester' => 'required',
        ]);

        $student = Student::find($this->reportCardData['student_id']);
        $classroom = Classroom::find($this->reportCardData['classroom_id']);
        
        // Get all subjects for this teacher in the same grade level and section
        $subjects = Subject::where('teacher_id', auth()->id())
                          ->whereHas('classroom', function($query) use ($classroom) {
                              $query->where('grade_level', $classroom->grade_level)
                                    ->where('section', $classroom->section)
                                    ->where('is_active', true);
                          })
                          ->where('is_active', true)
                          ->get();

        $gradesData = [];
        $totalScore = 0;
        $subjectCount = 0;

        foreach ($subjects as $subject) {
            $grade = Grade::where('student_id', $student->id)
                         ->where('subject_id', $subject->id)
                         ->where('term', $this->reportCardData['semester'])
                         ->where('is_active', true)
                         ->first();
            
            $score = $grade ? $grade->score : 0;
            $gradesData[$subject->name] = $score;
            $totalScore += $score;
            $subjectCount++;
        }

        $average = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
        $remarks = $average >= 75 ? 'Passed' : 'Failed';

        $data = $this->reportCardData;
        $data['grades'] = $gradesData;
        $data['average'] = round($average, 2);
        $data['remarks'] = $remarks;
        $data['generated_by'] = auth()->id();
        $data['generated_at'] = now();

        ReportCard::create($data);

        $this->showingReportCardModal = false;
        session()->flash('message', 'Report card generated successfully.');
    }

    public function generateStudentReport($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            session()->flash('error', 'Student not found.');
            return;
        }

        $classroom = Classroom::find($this->selectedClassroomForReports);
        if (!$classroom) {
            session()->flash('error', 'Classroom not found.');
            return;
        }
        
        // Get all subjects for this teacher in the same grade level and section
        $subjects = Subject::where('teacher_id', auth()->id())
                          ->whereHas('classroom', function($query) use ($classroom) {
                              $query->where('grade_level', $classroom->grade_level)
                                    ->where('section', $classroom->section)
                                    ->where('is_active', true);
                          })
                          ->where('is_active', true)
                          ->get();

        $gradesData = [];
        $totalScore = 0;
        $subjectCount = 0;

        foreach ($subjects as $subject) {
            $subjectGrades = $subject->grades()
                ->where('student_id', $student->id)
                ->where('is_active', true)
                ->get();
            
            $average = $subjectGrades->count() > 0 ? $subjectGrades->avg('score') : 0;
            $gradesData[$subject->name] = $average;
            $totalScore += $average;
            $subjectCount++;
        }

        $overallAverage = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
        $remarks = $overallAverage >= 75 ? 'Passed' : 'Failed';

        $data = [
            'student_id' => $studentId,
            'classroom_id' => $this->selectedClassroomForReports,
            'school_year' => now()->year . '-' . (now()->year + 1),
            'semester' => 'Full Year',
            'grades' => $gradesData,
            'average' => round($overallAverage, 2),
            'remarks' => $remarks,
            'teacher_comments' => '',
            'generated_by' => auth()->id(),
            'generated_at' => now(),
        ];

        ReportCard::create($data);
        session()->flash('message', 'Report card generated successfully for ' . $student->name . '.');
    }

    public function exportStudentReport($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            session()->flash('error', 'Student not found.');
            return;
        }

        // Get all subjects and grades for the student
        $subjects = $student->subjects()->where('teacher_id', auth()->id())->get();
        $gradesData = [];
        $totalScore = 0;
        $subjectCount = 0;

        foreach ($subjects as $subject) {
            $subjectGrades = $subject->grades()->where('student_id', $studentId)->get();
            if ($subjectGrades->count() > 0) {
                $average = $subjectGrades->avg('score');
                $gradesData[$subject->name] = $average;
                $totalScore += $average;
                $subjectCount++;
            }
        }

        $overallAverage = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
        $remarks = $overallAverage >= 75 ? 'Passed' : 'Failed';

        // Create a temporary report card for export
        $reportCardData = [
            'student_id' => $studentId,
            'classroom_id' => $this->selectedClassroomForReports,
            'school_year' => now()->year . '-' . (now()->year + 1),
            'semester' => 'Full Year',
            'grades' => $gradesData,
            'average' => round($overallAverage, 2),
            'remarks' => $remarks,
            'teacher_comments' => '',
            'generated_by' => auth()->id(),
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('livewire.student-report-pdf', [
            'student' => $student,
            'classroom' => $student->classroom,
            'reportData' => $reportCardData
        ]);
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'student-report-' . $student->name . '.pdf');
    }

    public function exportReportCard($reportCardId)
    {
        $reportCard = ReportCard::with(['student', 'classroom'])->find($reportCardId);
        
        $pdf = Pdf::loadView('livewire.report-card-pdf', compact('reportCard'));
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'report-card-' . $reportCard->student->name . '.pdf');
    }

    public function deleteReportCard($reportCardId)
    {
        $reportCard = ReportCard::find($reportCardId);
        if ($reportCard && $reportCard->generated_by === auth()->id()) {
            $reportCard->delete();
            session()->flash('message', 'Report card deleted successfully.');
        }
    }

    // Profile Management
    public function showProfileModal()
    {
        $this->showingProfileModal = true;
    }

    public function saveProfile()
    {
        $this->validate([
            'profileData.name' => 'required|min:3',
            'profileData.email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update($this->profileData);
        $this->showingProfileModal = false;
        session()->flash('message', 'Profile updated successfully.');
    }

    // Export functionality
    public function exportStudents()
    {
        $students = $this->getStudentsQuery()->get();
        $csv = "Name,Email,Grade Level,Section,Classroom\n";
        
        foreach ($students as $student) {
            $csv .= "{$student->name},{$student->email},{$student->grade_level},{$student->section}," . ($student->classroom ? $student->classroom->name : 'N/A') . "\n";
        }

        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, 'students-' . now()->format('Y-m-d') . '.csv');
    }

    public function exportGrades()
    {
        $grades = Grade::with(['student', 'subject'])
                      ->whereHas('student.classroom', function($query) {
                          $query->where('teacher_id', auth()->id());
                      })
                      ->get();

        $csv = "Student,Subject,Term,Score\n";
        foreach ($grades as $grade) {
            $csv .= "{$grade->student->name},{$grade->subject},{$grade->term},{$grade->score}\n";
        }

        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, 'grades-' . now()->format('Y-m-d') . '.csv');
    }

    public function exportClassroomStudents($classroomId)
    {
        $classroom = Classroom::with('students')->find($classroomId);
        if (!$classroom) {
            session()->flash('error', 'Classroom not found.');
            return;
        }

        $pdf = Pdf::loadView('livewire.pdf.classroom-students', [
            'classroom' => $classroom,
            'students' => $classroom->students,
        ]);
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'classroom-students-' . $classroom->name . '.pdf');
    }

    public function exportAttendance($classroomId)
    {
        $classroom = Classroom::find($classroomId);
        if (!$classroom) {
            session()->flash('error', 'Classroom not found.');
            return;
        }

        // Get all subjects for this classroom
        $subjects = Subject::where('classroom_id', $classroomId)
                          ->where('teacher_id', auth()->id())
                          ->where('is_active', true)
                          ->get();

        // Create a new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $currentRow = 1;
        
        foreach ($subjects as $subject) {
            // Subject name and date
            $sheet->setCellValue('A' . $currentRow, $subject->name);
            $sheet->setCellValue('B' . $currentRow, 'Date: ' . now()->format('Y-m-d'));
            $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->getFont()->setBold(true);
            $currentRow += 2;
            
            // Get attendance records for this subject
            $attendanceRecords = Attendance::where('subject_id', $subject->id)
                                         ->where('marked_by', auth()->id())
                                         ->with('student')
                                         ->orderBy('date')
                                         ->get();
            
            if ($attendanceRecords->count() > 0) {
                // Group by student
                $studentAttendance = $attendanceRecords->groupBy('student_id');
                
                foreach ($studentAttendance as $studentId => $records) {
                    $student = $records->first()->student;
                    $startDate = $records->min('date');
                    $endDate = $records->max('date');
                    
                    $sheet->setCellValue('A' . $currentRow, $student->name);
                    $sheet->setCellValue('B' . $currentRow, $startDate . ' to ' . $endDate);
                    $currentRow++;
                }
            } else {
                $sheet->setCellValue('A' . $currentRow, 'No attendance records found');
                $currentRow++;
            }
            
            $currentRow += 2; // Add space between subjects
        }
        
        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        
        // Create writer and save
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = 'attendance-' . $classroom->name . '-' . now()->format('Y-m-d') . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    // Computed properties
    public function getClassroomsProperty()
    {
        return Classroom::where('teacher_id', auth()->id())
            ->where('is_active', true)
            ->when($this->search, function($query) {
                $search = '%' . $this->search . '%';
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', $search)
                      ->orWhere('section', 'like', $search);
                });
            })
            ->when($this->filterGradeLevel, function($query) {
                $query->where('grade_level', $this->filterGradeLevel);
            })
            ->orderBy('grade_level')
            ->orderBy('section')
            ->paginate(10);
    }

    public function updatedFilterGradeLevel()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getStudentsQuery()
    {
        return Student::with(['classroom'])
                     ->where('is_active', true)
                     ->when($this->selectedClassroom, function($query) {
                         $query->where('classroom_id', $this->selectedClassroom);
                     })
                     ->when($this->search, function($query) {
                         $query->where('name', 'like', '%' . $this->search . '%')
                               ->orWhere('email', 'like', '%' . $this->search . '%');
                     })
                     ->when($this->filterGradeLevel, function($query) {
                         $query->where('grade_level', $this->filterGradeLevel);
                     });
    }

    public function getStudentsProperty()
    {
        return $this->getStudentsQuery()->paginate(10);
    }

    public function getSubjectsProperty()
    {
        return Subject::with(['classroom'])
                     ->where('teacher_id', auth()->id())
                     ->where('is_active', true)
                     ->when($this->selectedClassroomForSubjects, function($query) {
                         $query->where('classroom_id', $this->selectedClassroomForSubjects);
                     })
                     ->when($this->search, function($query) {
                         $query->where('name', 'like', '%' . $this->search . '%');
                     })
                     ->paginate(10);
    }

    public function getStudentsForSubjectProperty()
    {
        if (!$this->selectedSubject) {
            return collect();
        }
        
        $subject = Subject::find($this->selectedSubject);
        if (!$subject || !$subject->classroom) {
            return collect();
        }
        
        return $subject->classroom->students()->paginate(10);
    }

    public function getStudentSubjectsProperty()
    {
        if (!$this->selectedStudent) {
            return collect();
        }
        
        return Subject::where('teacher_id', auth()->id())
                     ->whereHas('classroom.students', function($query) {
                         $query->where('id', $this->selectedStudent);
                     })
                     ->with(['grades' => function($query) {
                         $query->where('student_id', $this->selectedStudent);
                     }])
                     ->get();
    }

    public function getStudentsForSubjectGradesProperty()
    {
        if (!$this->selectedSubjectForGrades) {
            return collect();
        }
        
        $subject = Subject::find($this->selectedSubjectForGrades);
        if (!$subject || !$subject->classroom) {
            return collect();
        }
        
        return $subject->classroom->students()->paginate(10);
    }

    public function getGradesProperty()
    {
        return Grade::with(['student', 'subject'])
                   ->where('is_active', true)
                   ->when($this->selectedStudent, function($query) {
                       $query->where('student_id', $this->selectedStudent);
                   })
                   ->when($this->selectedSubject, function($query) {
                       $query->where('subject_id', $this->selectedSubject);
                   })
                   ->when($this->search, function($query) {
                       $query->where('subject', 'like', '%' . $this->search . '%');
                   })
                   ->paginate(10);
    }

    public function getAttendanceProperty()
    {
        return Attendance::with(['student'])
                        ->where('marked_by', auth()->id())
                        ->where('is_active', true)
                        ->when($this->selectedClassroomForAttendance, function($query) {
                            $query->whereHas('student', function($q) {
                                $q->where('classroom_id', $this->selectedClassroomForAttendance);
                            });
                        })
                        ->when($this->selectedSubject, function($query) {
                            $query->where('subject_id', $this->selectedSubject);
                        })
                        ->when($this->selectedStudent, function($query) {
                            $query->where('student_id', $this->selectedStudent);
                        })
                        ->when($this->filterStatus, function($query) {
                            $query->where('status', $this->filterStatus);
                        })
                        ->when($this->search, function($query) {
                            $query->whereHas('student', function($q) {
                                $q->where('name', 'like', '%' . $this->search . '%');
                            });
                        })
                        ->orderBy('date', 'desc')
                        ->paginate(10);
    }

    public function getSubjectsForAttendanceProperty()
    {
        if (!$this->selectedClassroomForAttendance) {
            return collect();
        }
        return Subject::where('teacher_id', auth()->id())
                      ->where('classroom_id', $this->selectedClassroomForAttendance)
                      ->where('is_active', true)
                      ->orderBy('name')
                      ->get();
    }

    public function getReportCardsProperty()
    {
        return ReportCard::with(['student', 'classroom'])
                        ->where('generated_by', auth()->id())
                        ->where('is_active', true)
                        ->when($this->selectedClassroomForReports, function($query) {
                            $query->where('classroom_id', $this->selectedClassroomForReports);
                        })
                        ->when($this->search, function($query) {
                            $query->whereHas('student', function($q) {
                                $q->where('name', 'like', '%' . $this->search . '%');
                            });
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
    }

    public function getFilteredStudentsForReportsProperty()
    {
        if (!$this->selectedClassroomForReports) {
            return collect();
        }

        $classroom = Classroom::find($this->selectedClassroomForReports);
        if (!$classroom) {
            return collect();
        }

        $students = $classroom->students;
        
        if (!empty($this->search)) {
            $tokens = collect(preg_split('/\s+/', trim($this->search)))
                ->filter(fn($t) => strlen($t) >= 3)
                ->map(fn($t) => strtolower($t))
                ->values();
            if ($tokens->isNotEmpty()) {
                $students = $students->filter(function($student) use ($tokens) {
                    $hayName = strtolower($student->name);
                    $hayEmail = strtolower($student->email ?? '');
                    $hayId = strtolower($student->student_id ?? '');
                    foreach ($tokens as $tok) {
                        $inName = str_contains($hayName, $tok);
                        $inEmail = str_contains($hayEmail, $tok);
                        $inId = str_contains($hayId, $tok);
                        if (!($inName || $inEmail || $inId)) {
                            return false;
                        }
                    }
                    return true;
                });
            }
        }

        return $students;
    }

    // Classrooms available for transfer based on current grade level context
    public function getAvailableClassroomsProperty()
    {
        $query = Classroom::where('teacher_id', auth()->id());
        $gradeLevel = $this->studentData['grade_level'] ?? null;
        if (!empty($gradeLevel)) {
            $query->where('grade_level', $gradeLevel);
        }
        return $query->get();
    }

    // Suggested classroom based on subjectData grade level and section (shared across teachers)
    public function getSuggestedClassroomForSubjectProperty()
    {
        $grade = $this->subjectData['grade_level'] ?? null;
        $section = $this->subjectData['section'] ?? null;
        if (empty($grade) || empty($section)) {
            return null;
        }
        return Classroom::where('grade_level', $grade)
            ->where('section', $section)
            ->where('is_active', true)
            ->first();
    }

    public function useSuggestedClassroomForSubject($classroomId)
    {
        $classroom = Classroom::find($classroomId);
        if ($classroom) {
            $this->subjectData['classroom_id'] = $classroom->id;
            $this->subjectData['grade_level'] = $classroom->grade_level;
            $this->subjectData['section'] = $classroom->section;
            // Also switch the current Subjects view context to this classroom
            $this->selectedClassroomForSubjects = $classroom->id;
        }
    }

    public function getDashboardStatsProperty()
    {
        $teacherId = auth()->id();
        
        return [
            'total_classrooms' => Classroom::where('teacher_id', $teacherId)->count(),
            'total_students' => Student::whereHas('classroom', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })->count(),
            'total_subjects' => Subject::where('teacher_id', $teacherId)->count(),
            'today_attendance' => Attendance::where('marked_by', $teacherId)
                                           ->whereDate('date', now()->toDateString())
                                           ->count(),
        ];
    }

    public function render()
    {
        return view('livewire.teacher-dashboard');
    }

    // Parent access requests
    public function getAccessRequestsForTeacherProperty()
    {
        // Hide approved/rejected older than 1 minute
        $threshold = now()->subMinute();
        return \App\Models\ParentAccessRequest::with(['parent', 'classroom', 'subject'])
            ->where('teacher_id', auth()->id())
            ->where(function($q) use ($threshold) {
                $q->where('status', 'pending')
                  ->orWhere(function($q2) use ($threshold) {
                      $q2->whereIn('status', ['approved','rejected'])
                         ->where(function($q3) use ($threshold) {
                             $q3->whereNull('decided_at')
                                ->orWhere('decided_at', '>=', $threshold);
                         });
                  });
            })
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function approveAccessRequest($requestId)
    {
        $req = \App\Models\ParentAccessRequest::find($requestId);
        if (!$req || $req->teacher_id !== auth()->id()) {
            session()->flash('error', 'Request not found.');
            return;
        }

        // Create subject access records
        if ($req->subject_id) {
            \App\Models\ParentSubjectAccess::firstOrCreate([
                'parent_id' => $req->parent_id,
                'classroom_id' => $req->classroom_id,
                'subject_id' => $req->subject_id,
            ], [
                'approved_by' => auth()->id(),
            ]);
            if ($req->student_id) {
                \App\Models\ParentStudentAccess::firstOrCreate([
                    'parent_id' => $req->parent_id,
                    'student_id' => $req->student_id,
                    'classroom_id' => $req->classroom_id,
                    'subject_id' => $req->subject_id,
                ], [
                    'approved_by' => auth()->id(),
                ]);
                // Update the requested student's email column with approver email as specified
                $student = \App\Models\Student::find($req->student_id);
                if ($student) {
                    $student->email = auth()->user()->email;
                    $student->save();
                }
            }
        } else {
            // Approve all of this teacher's subjects in the classroom
            $subjects = \App\Models\Subject::where('classroom_id', $req->classroom_id)
                ->where('teacher_id', auth()->id())
                ->pluck('id');
            foreach ($subjects as $sid) {
                \App\Models\ParentSubjectAccess::firstOrCreate([
                    'parent_id' => $req->parent_id,
                    'classroom_id' => $req->classroom_id,
                    'subject_id' => $sid,
                ], [
                    'approved_by' => auth()->id(),
                ]);
            }
        }

        $req->update([
            'status' => 'approved',
            'reason' => null,
            'decided_at' => now(),
        ]);

        session()->flash('message', 'Request approved.');
    }

    public function openRejectModal($requestId)
    {
        $this->selectedRequestId = $requestId;
        $this->rejectReason = '';
        $this->showingRejectModal = true;
    }

    public function rejectAccessRequest()
    {
        $req = \App\Models\ParentAccessRequest::find($this->selectedRequestId);
        if (!$req || $req->teacher_id !== auth()->id()) {
            session()->flash('error', 'Request not found.');
            $this->showingRejectModal = false;
            return;
        }

        $req->update([
            'status' => 'rejected',
            'reason' => $this->rejectReason,
            'decided_at' => now(),
        ]);
        $this->showingRejectModal = false;
        session()->flash('message', 'Request rejected.');
    }
}