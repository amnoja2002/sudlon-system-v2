<?php

namespace App\Livewire\Roles\Teacher;

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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\ExcelHelper;

#[Layout('layouts.app')]
class Dashboard extends Component
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
    public $showingReportScopeModal = false;
    public $showingProfileModal = false;
    public $showingAttendanceForm = false;
    public $confirmingDeleteClassroomId = null;
    public $confirmingDeleteAttendanceId = null;
    public $showingDeleteStudentModal = false;
    public $studentIdToDelete = null;
    public $showingDeleteSubjectModal = false;
    public $subjectIdToDelete = null;

    // Requests moderation
    // Requests moderation (disabled)
    // public $showingRejectModal = false;
    // public $selectedRequestId = null;
    // public $rejectReason = '';

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
        'first_name' => '',
        'last_name' => '',
        'email' => null,
        'grade_level' => '',
        'section' => '',
        'classroom_id' => null,
        'guardian_first_name' => null,
        'guardian_last_name' => null,
        'guardian_contact' => null,
        // Independent parent/guardian toggles & details
        'mother_enabled' => false,
        'mother_first_name' => null,
        'mother_last_name' => null,
        'mother_email' => null,
        'mother_contact' => null,
        'father_enabled' => false,
        'father_first_name' => null,
        'father_last_name' => null,
        'father_email' => null,
        'father_contact' => null,
        'guardian_enabled' => false,
        'guardian_email' => null,
    ];

    public $subjectData = [
        'name' => '',
        'description' => '',
        'classroom_id' => null,
        'grade_level' => '',
        'section' => '',
    ];

    // Selection helper removed; now subjects are always created new, existing list is view-only
    public $subjectNameChoice = '';

    // New properties for classroom selection
    public $selectedGradeLevel = '';
    public $selectedSection = '';
    public $availableSections = [];
    public $existingClassrooms = [];
    public $classroomSelectionMode = 'select'; // 'select' or 'create'
    
    // New properties for classroom creation with existing sections
    public $classroomGradeLevel = '';
    public $classroomSection = '';
    public $availableClassroomSections = [];
    public $existingClassroomsForGrade = [];
    public $classroomSelectionModeForClassroom = 'select'; // 'select' or 'create'
    public $newSectionName = '';
    public $lockMaxStudents = false;

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

    // Attendance log view state
    public $currentAttendanceView = 'form'; // 'form' | 'log'
    public $attendanceLogDate = null;

    public $studentAttendanceData = [];

    public $reportCardData = [
        'student_id' => null,
        'classroom_id' => null,
        'school_year' => '',
        'semester' => '',
        'teacher_comments' => '',
    ];
    public $reportScope = 'mine'; // 'mine' | 'all'
    public $pendingReportStudentId = null;
    public $confirmingDeleteReportCardId = null;

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
            $this->classroomData = $this->selectedClassroom->only(['grade_level', 'section', 'description', 'max_students']);
        } else {
            $this->reset('classroomData');
            // no name column anymore
        }
        
        // Reset classroom selection properties
        $this->reset(['classroomGradeLevel', 'classroomSection', 'availableClassroomSections', 'existingClassroomsForGrade', 'classroomSelectionModeForClassroom']);
        
        $this->newSectionName = '';
        $this->showingClassroomModal = true;
    }

    public function updated($field)
    {
        if ($field === 'classroomData.grade_level') {
            // When grade changes, clear section and reload available sections and existing classrooms list
            $this->classroomData['section'] = '';
            $grade = $this->classroomData['grade_level'] ?? '';
            $this->availableClassroomSections = [];
            $this->existingClassroomsForGrade = [];
            $this->newSectionName = '';
            $this->classroomSelectionModeForClassroom = 'select';
            $this->lockMaxStudents = false;

            if (!empty($grade)) {
                $this->availableClassroomSections = Classroom::where('grade_level', $grade)
                    ->where('is_active', true)
                    ->distinct()
                    ->pluck('section')
                    ->sort()
                    ->values()
                    ->toArray();

                $this->existingClassroomsForGrade = Classroom::where('grade_level', $grade)
                    ->where('is_active', true)
                    ->with(['teacher', 'students'])
                    ->get()
                    ->map(function($classroom) {
                        return [
                            'id' => $classroom->id,
                            'name' => $classroom->display_name,
                            'section' => $classroom->section,
                            'teacher_name' => $classroom->teacher->name,
                            'student_count' => $classroom->students->count(),
                            'description' => $classroom->description,
                        ];
                    });
            }
        }
        if ($field === 'classroomData.section') {
            $section = $this->classroomData['section'] ?? null;
            if ($section !== 'new') {
                // Clear custom section input when not creating a new section
                $this->newSectionName = '';
            }

            // When a concrete section is selected, try to auto-fill and lock max_students from an existing classroom
            $grade = $this->classroomData['grade_level'] ?? null;
            if (!empty($grade) && !empty($section) && $section !== 'new') {
                $existing = Classroom::where('grade_level', $grade)
                    ->where('section', $section)
                    ->where('is_active', true)
                    ->first();
                if ($existing) {
                    $this->classroomData['max_students'] = $existing->max_students;
                    $this->lockMaxStudents = true;
                } else {
                    $this->lockMaxStudents = false;
                }
            } else {
                $this->lockMaxStudents = false;
            }
        }
    }

    // When choosing an existing classroom as template, mirror max_students and lock it via the UI
    // removed updatedSelectedExistingClassroom (no longer used)

    public function saveClassroom()
    {
        // Base validation
        $this->validate([
            'classroomData.grade_level' => 'required',
            'classroomData.max_students' => 'required|integer|min:1|max:50',
        ]);

        $data = $this->classroomData;
        $data['teacher_id'] = auth()->id();

        // Normalize inputs
        $data['grade_level'] = is_string($data['grade_level']) ? trim($data['grade_level']) : $data['grade_level'];
        $selectedSection = trim((string)($data['section'] ?? ''));
        if (strtolower($selectedSection) === 'new') {
            // Validate the new section name and substitute it
            $this->validate([
                'newSectionName' => ['required','string','min:1','max:50'],
            ]);
            $data['section'] = trim($this->newSectionName);
        } else {
            $this->validate([
                'classroomData.section' => 'required',
            ]);
            $data['section'] = $selectedSection;
        }

        // Enforce unique active classroom per teacher (teacher_id, grade_level, section)
        $activeExists = Classroom::where('teacher_id', auth()->id())
            ->where('grade_level', $data['grade_level'])
            ->where('section', $data['section'])
            ->where('is_active', true)
            ->when($this->selectedClassroom, fn($q) => $q->where('id', '!=', $this->selectedClassroom->id))
            ->exists();
        if ($activeExists) {
            session()->flash('error', 'You already have an active classroom with this grade level and section.');
            return;
        }

        // If an inactive classroom exists for same teacher/grade/section, reactivate instead of creating new
        $inactive = Classroom::where('teacher_id', auth()->id())
            ->where('grade_level', $data['grade_level'])
            ->where('section', $data['section'])
            ->where('is_active', false)
            ->first();
        if ($inactive && !$this->selectedClassroom) {
            $inactive->update(array_merge($data, ['is_active' => true]));
            // Reactivate students that were deactivated when the classroom was deleted
            Student::where('classroom_id', $inactive->id)->update(['is_active' => true]);
            $this->showingClassroomModal = false;
            session()->flash('message', 'Classroom reactivated successfully.');
            return;
        }

        if ($this->selectedClassroom) {
            $this->selectedClassroom->update($data);
            $message = 'Classroom updated successfully.';
        } else {
            $newClassroom = Classroom::create($data);
            
            // Auto-detect a source classroom for the same grade & section and copy students
            $sourceClassroomId = null;
            if (!$sourceClassroomId) {
                $source = Classroom::where('grade_level', $data['grade_level'])
                    ->where('section', $data['section'])
                    ->where('is_active', true)
                    ->where('id', '!=', $newClassroom->id)
                    ->whereHas('students', function($q){
                        $q->where('is_active', true);
                    })
                    ->latest('id')
                    ->first();
                $sourceClassroomId = $source?->id;
            }

            if ($sourceClassroomId) {
                $copied = $this->copyClassroomWithStudents($sourceClassroomId, $newClassroom);
                $message = $copied > 0
                    ? ("Classroom created and {$copied} students copied.")
                    : 'Classroom created. No students were available to copy.';
            } else {
                $message = 'Classroom created successfully.';
            }
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
        // Close modal if open
        $this->confirmingDeleteClassroomId = null;
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
            'studentData.first_name' => 'required|string|min:1',
            'studentData.last_name' => 'required|string|min:1',
            // student email removed
            'studentData.mother_email' => 'nullable|email',
            'studentData.father_email' => 'nullable|email',
            'studentData.guardian_email' => 'nullable|email',
            'studentData.grade_level' => 'required',
            'studentData.section' => 'required',
            'studentData.classroom_id' => 'required|exists:classrooms,id',
            'studentData.guardian_first_name' => 'nullable|string|max:100',
            'studentData.guardian_last_name' => 'nullable|string|max:100',
            'studentData.guardian_contact' => 'nullable|string|max:30',
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
                $name = trim(($this->studentData['first_name'] ?? '') . ' ' . ($this->studentData['last_name'] ?? ''));
                $q->where('name', $name);
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

        // Keep legacy name in sync for compatibility
        $this->studentData['name'] = trim(($this->studentData['first_name'] ?? '') . ' ' . ($this->studentData['last_name'] ?? ''));

        if ($this->selectedStudent) {
            $this->selectedStudent->update($this->studentData);
            $message = 'Student updated successfully.';
        } else {
            $createdStudent = Student::create($this->studentData);
            $message = 'Student added successfully.';

            // Also add this student to other active classrooms with the same grade & section (used by other teachers)
            $gradeLevel = $createdStudent->grade_level;
            $section = $createdStudent->section;
            $sourceClassroomId = $createdStudent->classroom_id;

            $otherClassrooms = Classroom::where('grade_level', $gradeLevel)
                ->where('section', $section)
                ->where('is_active', true)
                ->where('id', '!=', $sourceClassroomId)
                ->get();

            foreach ($otherClassrooms as $targetClassroom) {
                // Avoid duplicates by name within the target classroom context
                $nameInTarget = trim((string)($this->studentData['first_name'] ?? '') . ' ' . (string)($this->studentData['last_name'] ?? ''));
                $exists = Student::where('is_active', true)
                    ->where('classroom_id', $targetClassroom->id)
                    ->where('name', $nameInTarget)
                    ->exists();
                if ($exists) {
                    continue;
                }

                Student::create([
                    'name' => $nameInTarget,
                    'first_name' => $this->studentData['first_name'] ?? null,
                    'last_name' => $this->studentData['last_name'] ?? null,
                    'grade_level' => $gradeLevel,
                    'section' => $section,
                    'classroom_id' => $targetClassroom->id,
                    'is_active' => true,
                    // Copy parent/guardian info
                    'mother_enabled' => (bool)($this->studentData['mother_enabled'] ?? false),
                    'mother_first_name' => $this->studentData['mother_first_name'] ?? null,
                    'mother_last_name' => $this->studentData['mother_last_name'] ?? null,
                    'mother_email' => $this->studentData['mother_email'] ?? null,
                    'mother_contact' => $this->studentData['mother_contact'] ?? null,
                    'father_enabled' => (bool)($this->studentData['father_enabled'] ?? false),
                    'father_first_name' => $this->studentData['father_first_name'] ?? null,
                    'father_last_name' => $this->studentData['father_last_name'] ?? null,
                    'father_email' => $this->studentData['father_email'] ?? null,
                    'father_contact' => $this->studentData['father_contact'] ?? null,
                    'guardian_enabled' => (bool)($this->studentData['guardian_enabled'] ?? false),
                    'guardian_first_name' => $this->studentData['guardian_first_name'] ?? null,
                    'guardian_last_name' => $this->studentData['guardian_last_name'] ?? null,
                    'guardian_email' => $this->studentData['guardian_email'] ?? null,
                    'guardian_contact' => $this->studentData['guardian_contact'] ?? null,
                ]);
            }
        }

        $this->showingStudentModal = false;
        session()->flash('message', $message);
    }

    public function toggleGuardianType(string $type): void
    {
        // Switch to independent toggles
        if ($type === 'mother') {
            $this->studentData['mother_enabled'] = !($this->studentData['mother_enabled'] ?? false);
            if (!$this->studentData['mother_enabled']) {
                $this->studentData['mother_first_name'] = null;
                $this->studentData['mother_last_name'] = null;
                $this->studentData['mother_email'] = null;
                $this->studentData['mother_contact'] = null;
            }
        } elseif ($type === 'father') {
            $this->studentData['father_enabled'] = !($this->studentData['father_enabled'] ?? false);
            if (!$this->studentData['father_enabled']) {
                $this->studentData['father_first_name'] = null;
                $this->studentData['father_last_name'] = null;
                $this->studentData['father_email'] = null;
                $this->studentData['father_contact'] = null;
            }
        } elseif ($type === 'guardian') {
            $this->studentData['guardian_enabled'] = !($this->studentData['guardian_enabled'] ?? false);
            if (!$this->studentData['guardian_enabled']) {
                $this->studentData['guardian_first_name'] = null;
                $this->studentData['guardian_last_name'] = null;
                $this->studentData['guardian_email'] = null;
                $this->studentData['guardian_contact'] = null;
            }
        }
    }

    

    public function backToClassrooms()
    {
        $this->setView('classrooms');
    }

    public function deleteStudent($studentId)
    {
        // Backward-compatible direct delete (still callable), but prefer modal flow
        $student = Student::find($studentId);
        if ($student) {
            $student->delete();
            session()->flash('message', 'Student deleted successfully.');
        }
    }

    public function openDeleteStudentModal($studentId)
    {
        $this->studentIdToDelete = $studentId;
        $this->showingDeleteStudentModal = true;
    }

    public function confirmDeleteStudent()
    {
        $student = $this->studentIdToDelete ? Student::find($this->studentIdToDelete) : null;
        if ($student) {
            $student->delete();
            session()->flash('message', 'Student deleted successfully.');
        } else {
            session()->flash('error', 'Student not found.');
        }
        $this->studentIdToDelete = null;
        $this->showingDeleteStudentModal = false;
    }

    public function cancelDeleteStudent()
    {
        $this->studentIdToDelete = null;
        $this->showingDeleteStudentModal = false;
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
            $this->subjectNameChoice = $this->subjectData['name'] ?? '';
            $classroom = Classroom::find($this->subjectData['classroom_id']);
            if ($classroom) {
                $this->selectedGradeLevel = $classroom->grade_level;
                $this->selectedSection = $classroom->section;
            }
        } else {
            $this->reset('subjectData');
            $this->subjectNameChoice = '';
            $this->reset(['selectedGradeLevel', 'selectedSection', 'availableSections', 'existingClassrooms', 'classroomSelectionMode']);
            $this->subjectData['classroom_id'] = $this->selectedClassroomForSubjects;
            if ($this->selectedClassroomForSubjects) {
                $classroom = Classroom::find($this->selectedClassroomForSubjects);
                if ($classroom) {
                    $this->selectedGradeLevel = $classroom->grade_level;
                    $this->selectedSection = $classroom->section;
                }
            }
        }
        $this->showingSubjectModal = true;
    }

    public function updatedSubjectNameChoice($value)
    {
        // If picking an existing name, mirror it into subjectData.name; if creating new, leave name for text input
        if (!empty($value) && $value !== '__new__') {
            $this->subjectData['name'] = $value;
        } elseif ($value === '__new__') {
            // Clear existing name to allow fresh input
            $this->subjectData['name'] = '';
        }
    }

    // When classroom is changed in the subject form, sync grade/section from target classroom
    public function updatedSubjectDataClassroomId($classroomId): void
    {
        if (!$classroomId) {
            return;
        }

        $classroom = Classroom::find($classroomId);
        if ($classroom) {
            $this->selectedGradeLevel = $classroom->grade_level;
            $this->selectedSection = $classroom->section;
        }
    }

    public function saveSubject()
    {
        // Handle classroom creation if needed
        if ($this->classroomSelectionMode === 'create' && $this->selectedGradeLevel && $this->selectedSection) {
            // Check if classroom already exists (in case it was created by another teacher)
            $existingClassroom = Classroom::where('grade_level', $this->selectedGradeLevel)
                ->where('section', $this->selectedSection)
                ->where('is_active', true)
                ->first();
            
            if ($existingClassroom) {
                $this->subjectData['classroom_id'] = $existingClassroom->id;
                $this->classroomSelectionMode = 'select';
            } else {
                // Create new classroom
                $classroomData = [
                    'name' => 'Grade ' . $this->selectedGradeLevel . ' - ' . $this->selectedSection,
                    'grade_level' => $this->selectedGradeLevel,
                    'section' => $this->selectedSection,
                    'description' => 'Grade ' . $this->selectedGradeLevel . ' Section ' . $this->selectedSection,
                    'teacher_id' => auth()->id(),
                    'max_students' => 40,
                    'is_active' => true,
                ];
                
                $newClassroom = Classroom::create($classroomData);
                $this->subjectData['classroom_id'] = $newClassroom->id;
            }
        }

        $this->validate([
            'subjectData.name' => ['required','string','min:2','max:50','regex:/^[A-Za-z0-9\s\-\.\&]+$/'],
            'subjectData.classroom_id' => 'required|exists:classrooms,id',
        ]);

        $data = $this->subjectData;
        $data['teacher_id'] = auth()->id();

        // Prevent creating duplicate ACTIVE subject names within the same classroom
        $conflict = Subject::where('classroom_id', $data['classroom_id'])
            ->where('name', $data['name'])
            ->where('is_active', true)
            ->when($this->selectedSubject, fn($q) => $q->where('id', '!=', $this->selectedSubject->id))
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

    public function openDeleteSubjectModal($subjectId)
    {
        $this->subjectIdToDelete = $subjectId;
        $this->showingDeleteSubjectModal = true;
    }

    public function confirmDeleteSubject()
    {
        $subject = $this->subjectIdToDelete ? Subject::find($this->subjectIdToDelete) : null;
        if ($subject && $subject->teacher_id === auth()->id()) {
            $subject->delete();
            session()->flash('message', 'Subject deleted successfully.');
        } else {
            session()->flash('error', 'Subject not found or not owned by you.');
        }
        $this->subjectIdToDelete = null;
        $this->showingDeleteSubjectModal = false;
    }

    public function cancelDeleteSubject()
    {
        $this->subjectIdToDelete = null;
        $this->showingDeleteSubjectModal = false;
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

    public function openDeleteAttendanceModal($attendanceId)
    {
        $this->confirmingDeleteAttendanceId = $attendanceId;
    }

    public function confirmDeleteAttendance()
    {
        $attendanceId = $this->confirmingDeleteAttendanceId;
        $this->confirmingDeleteAttendanceId = null;
        $this->deleteAttendance($attendanceId);
    }

    public function cancelDeleteAttendance()
    {
        $this->confirmingDeleteAttendanceId = null;
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

    // Attendance Log navigation
    public function showAttendanceLog()
    {
        if (!$this->attendanceLogDate) {
            $this->attendanceLogDate = now()->toDateString();
        }
        $this->currentAttendanceView = 'log';
    }

    public function backToAttendanceFormView()
    {
        $this->currentAttendanceView = 'form';
    }

    public function nextAttendanceLogDay()
    {
        $date = $this->attendanceLogDate ? \Carbon\Carbon::parse($this->attendanceLogDate) : now();
        $this->attendanceLogDate = $date->copy()->addDay()->toDateString();
    }

    public function prevAttendanceLogDay()
    {
        $date = $this->attendanceLogDate ? \Carbon\Carbon::parse($this->attendanceLogDate) : now();
        $this->attendanceLogDate = $date->copy()->subDay()->toDateString();
    }

    public function getAttendanceLogProperty()
    {
        if (!$this->selectedClassroomForAttendance || !$this->selectedSubject) {
            return collect();
        }
        $date = $this->attendanceLogDate ?: now()->toDateString();
        return Attendance::with(['student'])
            ->where('is_active', true)
            ->where('subject_id', $this->selectedSubject)
            ->whereHas('student', function($q) {
                $q->where('classroom_id', $this->selectedClassroomForAttendance);
            })
            ->whereDate('date', $date)
            ->orderBy('student_id')
            ->get();
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

            // Ignore already recorded statuses (prefixed with 'recorded_')
            if (is_string($status) && str_starts_with($status, 'recorded_')) {
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
        $this->reportScope = 'mine';
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
        
        // Get all subjects for any teacher in the same grade level and section
        $subjectsQuery = Subject::whereHas('classroom', function($query) use ($classroom) {
                $query->where('grade_level', $classroom->grade_level)
                      ->where('section', $classroom->section)
                      ->where('is_active', true);
            })
            ->where('is_active', true);

        if (($this->reportScope ?? 'mine') === 'mine') {
            $subjectsQuery->where('teacher_id', auth()->id());
        }

        $subjects = $subjectsQuery->get();

        $gradesData = [];
        $totalScore = 0;
        $subjectCount = 0;

        foreach ($subjects as $subject) {
            // Map student to the corresponding record in the subject's classroom by name
            $targetStudentId = $student->id;
            if ($subject->classroom_id !== $classroom->id) {
                $targetStudentId = Student::where('classroom_id', $subject->classroom_id)
                    ->where('name', $student->name)
                    ->where('is_active', true)
                    ->value('id');
            }

            $score = 0;
            if ($targetStudentId) {
                // Support both subject_id link and legacy subject name
                $grade = Grade::where('student_id', $targetStudentId)
                             ->where('term', $this->reportCardData['semester'])
                             ->where('is_active', true)
                             ->where(function($q) use ($subject) {
                                 $q->where('subject_id', $subject->id)
                                   ->orWhere('subject', $subject->name);
                             })
                             ->first();
                if ($grade) {
                    $score = (float)$grade->score;
                }
            }

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
        $data['scope'] = ($this->reportScope ?? 'mine');
        $data['generated_at'] = now();

        ReportCard::create($data);

        $this->showingReportCardModal = false;
        session()->flash('message', 'Report card generated successfully.');
    }

    public function generateStudentReport($studentId)
    {
        // Open scope chooser modal first
        $this->pendingReportStudentId = $studentId;
        $this->reportScope = 'mine';
        $this->showingReportScopeModal = true;
    }

    public function openReportScopeModal($studentId)
    {
        $this->pendingReportStudentId = $studentId;
        $this->reportScope = 'mine';
        $this->showingReportScopeModal = true;
    }

    public function confirmReportScope()
    {
        $studentId = $this->pendingReportStudentId;
        $this->pendingReportStudentId = null;
        $this->showingReportScopeModal = false;

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

        // Subjects selection based on scope
        $subjectsQuery = Subject::query()
            ->whereHas('classroom', function($query) use ($classroom) {
                $query->where('grade_level', $classroom->grade_level)
                      ->where('section', $classroom->section)
                      ->where('is_active', true);
            })
            ->where('is_active', true);

        if (($this->reportScope ?? 'mine') === 'mine') {
            $subjectsQuery->where('teacher_id', auth()->id());
        }

        $subjects = $subjectsQuery->get();

        $gradesData = [];
        $totalScore = 0;
        $subjectCount = 0;

        foreach ($subjects as $subject) {
            // Map student to the matching record in the subject's classroom by name
            $targetStudentId = $student->id;
            if ($subject->classroom_id !== $this->selectedClassroomForReports) {
                $targetStudentId = Student::where('classroom_id', $subject->classroom_id)
                    ->where('name', $student->name)
                    ->where('is_active', true)
                    ->value('id');
            }

            $average = 0;
            if ($targetStudentId) {
                // Include grades linked by subject_id OR legacy subject name
                $subjectGrades = Grade::where('student_id', $targetStudentId)
                    ->where('is_active', true)
                    ->where(function($q) use ($subject) {
                        $q->where('subject_id', $subject->id)
                          ->orWhere('subject', $subject->name);
                    })
                    ->get();
                $average = $subjectGrades->count() > 0 ? (float)$subjectGrades->avg('score') : 0;
            }

            $gradesData[$subject->name] = $average;
            $totalScore += $average;
            $subjectCount++;
        }

        $overallAverage = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
        $remarks = $overallAverage >= 75 ? 'Passed' : 'Failed';

        $data = [
            'student_id' => $student->id,
            'classroom_id' => $this->selectedClassroomForReports,
            'school_year' => now()->year . '-' . (now()->year + 1),
            'semester' => 'Full Year',
            'grades' => $gradesData,
            'average' => round($overallAverage, 2),
            'remarks' => $remarks,
            'teacher_comments' => '',
            'generated_by' => auth()->id(),
            'generated_at' => now(),
            'scope' => ($this->reportScope ?? 'mine'),
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

        $pdf = Pdf::loadView('pdf.student-report', [
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
        
        $pdf = Pdf::loadView('pdf.report-card', compact('reportCard'));
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

    public function openDeleteReportCardModal($reportCardId)
    {
        $this->confirmingDeleteReportCardId = $reportCardId;
    }

    public function confirmDeleteReportCard()
    {
        $id = $this->confirmingDeleteReportCardId;
        $this->confirmingDeleteReportCardId = null;
        if ($id) {
            $this->deleteReportCard($id);
        }
    }

    public function cancelDeleteReportCard()
    {
        $this->confirmingDeleteReportCardId = null;
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
        
        $csv = view('excel.students-export', compact('students'))->render();

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

        $csv = view('excel.grades-export', compact('grades'))->render();

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

        $pdf = Pdf::loadView('pdf.classroom-students', [
            'classroom' => $classroom,
            'students' => $classroom->students,
        ]);
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'classroom-students-' . $classroom->display_name . '.pdf');
    }

    public function exportAttendance($classroomId)
    {
        $classroom = Classroom::find($classroomId);
        if (!$classroom) {
            session()->flash('error', 'Classroom not found.');
            return;
        }

        // Get all active subjects for this classroom taught by current teacher
        $subjects = Subject::where('classroom_id', $classroomId)
                          ->where('teacher_id', auth()->id())
                          ->where('is_active', true)
                          ->orderBy('name')
                          ->get();

        // Get students in the classroom
        $students = $classroom->students()->where('is_active', true)->orderBy('name')->get();

        // Create a new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Generate one worksheet per subject with columns per attendance date
        $sheetIndex = 0;
        foreach ($subjects as $subject) {
            $sheet = $sheetIndex === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet($sheetIndex);
            $sheet->setTitle(substr($subject->name, 0, 31));

            // Distinct attendance dates for this subject (only students in this classroom)
            // Normalize to date-only (YYYY-MM-DD)
            $dates = Attendance::where('subject_id', $subject->id)
                               ->whereIn('student_id', $students->pluck('id'))
                               ->where('is_active', true)
                               ->orderBy('date')
                               ->pluck('date')
                               ->map(function($d){
                                   return \Carbon\Carbon::parse($d)->toDateString();
                               })
                               ->unique()
                               ->values();

            // Header
            $sheet->setCellValue('A1', 'Subject: ' . $subject->name);
            $headerRow = 2;
            $colIdx = 1; // A
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue($colLetter . $headerRow, 'Student Name');
            $colIdx++;
            foreach ($dates as $date) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue($colLetter . $headerRow, (string)$date);
                $colIdx++;
            }
            $lastHeaderCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(max(1, $dates->count() + 1));
            $sheet->getStyle('A' . $headerRow . ':' . $lastHeaderCol . $headerRow)->getFont()->setBold(true);

            // Preload attendance map for fast lookup
            $attendanceMap = Attendance::where('subject_id', $subject->id)
                                       ->whereIn('student_id', $students->pluck('id'))
                                       ->where('is_active', true)
                                       ->get()
                                       ->groupBy(function($att){
                                           return $att->student_id . '|' . \Carbon\Carbon::parse($att->date)->toDateString();
                                       });

            // Rows per student
            $row = $headerRow + 1;
            foreach ($students as $student) {
                $colIdx = 1;
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue($colLetter . $row, $student->name);
                $colIdx++;
                foreach ($dates as $date) {
                    $key = $student->id . '|' . (string)$date;
                    $value = '';
                    if (isset($attendanceMap[$key])) {
                        $att = $attendanceMap[$key]->first();
                        if ($att && strtolower((string)$att->status) === 'present') {
                            $value = 'Present';
                        }
                    }
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                    $sheet->setCellValue($colLetter . $row, $value);
                    $colIdx++;
                }
                $row++;
            }

            // Auto-size columns
            for ($i = 1; $i <= max(1, $dates->count() + 1); $i++) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                $sheet->getColumnDimension($letter)->setAutoSize(true);
            }

            $sheetIndex++;
        }

        // Create writer and save
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'attendance-' . $classroom->display_name . '-' . now()->format('Y-m-d') . '.xlsx';

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
                    // search by section or grade_level since `name` column was dropped
                    $q->where('section', 'like', $search)
                      ->orWhere('grade_level', 'like', $search);
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
                     ->whereHas('classroom', function($q) {
                         $q->where('teacher_id', auth()->id());
                     })
                     ->when($this->selectedClassroom, function($query) {
                         $query->where('classroom_id', $this->selectedClassroom);
                     })
                     ->when($this->search, function($query) {
                         $query->where(function($qq){
                             $qq->where('name', 'like', '%' . $this->search . '%');
                         });
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
                        ->where(function($q){
                            // Show both my generated cards and general ones
                            $q->where('generated_by', auth()->id())
                              ->orWhere('scope', 'all');
                        })
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

    // All active classrooms for the current teacher (for subject selection)
    public function getTeacherClassroomsProperty()
    {
        return Classroom::where('teacher_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('grade_level')
            ->orderBy('section')
            ->get();
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

    // New methods for classroom selection system
    public function updatedSelectedGradeLevel()
    {
        $this->selectedSection = '';
        $this->availableSections = [];
        $this->existingClassrooms = [];
        $this->subjectData['classroom_id'] = null;
        
        if ($this->selectedGradeLevel) {
            // Get all sections for this grade level
            $this->availableSections = Classroom::where('grade_level', $this->selectedGradeLevel)
                ->where('is_active', true)
                ->distinct()
                ->pluck('section')
                ->sort()
                ->values()
                ->toArray();
            
            // Get existing classrooms for this grade level
            $this->existingClassrooms = Classroom::where('grade_level', $this->selectedGradeLevel)
                ->where('is_active', true)
                ->with(['teacher', 'students'])
                ->get()
                ->map(function($classroom) {
                    return [
                        'id' => $classroom->id,
                        'name' => $classroom->display_name,
                        'section' => $classroom->section,
                        'teacher_name' => $classroom->teacher->name,
                        'student_count' => $classroom->students->count(),
                        'description' => $classroom->description,
                    ];
                });
        }
    }

    public function updatedSelectedSection()
    {
        $this->subjectData['classroom_id'] = null;
        
        if ($this->selectedGradeLevel && $this->selectedSection) {
            // Find existing classroom for this grade level and section
            $existingClassroom = Classroom::where('grade_level', $this->selectedGradeLevel)
                ->where('section', $this->selectedSection)
                ->where('is_active', true)
                ->first();
            
            if ($existingClassroom) {
                $this->subjectData['classroom_id'] = $existingClassroom->id;
                $this->classroomSelectionMode = 'select';
            } else {
                $this->classroomSelectionMode = 'create';
            }
        }
    }

    public function selectExistingClassroom($classroomId)
    {
        $this->subjectData['classroom_id'] = $classroomId;
        $this->classroomSelectionMode = 'select';
        
        $classroom = Classroom::find($classroomId);
        if ($classroom) {
            $this->selectedSection = $classroom->section;
        }
    }

    public function createNewClassroom()
    {
        $this->classroomSelectionMode = 'create';
        $this->subjectData['classroom_id'] = null;
    }

    public function getExistingSubjectsForClassroom($classroomId)
    {
        return Subject::where('classroom_id', $classroomId)
            ->where('is_active', true)
            ->with('teacher')
            ->get()
            ->map(function($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'teacher_name' => $subject->teacher->name,
                    'description' => $subject->description,
                ];
            });
    }

    // Methods for classroom creation with existing sections
    // removed: updatedClassroomGradeLevel (unused after binding to classroomData.*)

    public function updatedClassroomDataGradeLevel()
    {
        $this->classroomData['section'] = '';
        $this->availableClassroomSections = [];
        $this->existingClassroomsForGrade = [];
        $this->classroomSelectionModeForClassroom = 'select';
        
        if ($this->classroomData['grade_level']) {
            // Get all sections for this grade level
            $this->availableClassroomSections = Classroom::where('grade_level', $this->classroomData['grade_level'])
                ->where('is_active', true)
                ->distinct()
                ->pluck('section')
                ->sort()
                ->values()
                ->toArray();
            
            // Get existing classrooms for this grade level
            $this->existingClassroomsForGrade = Classroom::where('grade_level', $this->classroomData['grade_level'])
                ->where('is_active', true)
                ->with(['teacher', 'students'])
                ->get()
                ->map(function($classroom) {
                    return [
                        'id' => $classroom->id,
                        'name' => $classroom->name,
                        'section' => $classroom->section,
                        'teacher_name' => $classroom->teacher->name,
                        'student_count' => $classroom->students->count(),
                        'description' => $classroom->description,
                    ];
                });
        }
    }

    // removed: updatedClassroomSection (unused)

    // removed: selectExistingClassroomForCopy (unused after auto-copy)

    // removed: createNewClassroomSection (unused)

    public function copyClassroomWithStudents($sourceClassroomId, $newClassroom)
    {
        $sourceClassroom = Classroom::find($sourceClassroomId);
        
        if (!$sourceClassroom) {
            return 0;
        }

        // Copy students from source classroom to new classroom
        $students = $sourceClassroom->students()->where('is_active', true)->get();
        $copiedCount = 0;
        
        foreach ($students as $student) {
            // Create a new student record for the new classroom
            Student::create([
                'name' => trim((string)($student->first_name ?? '') . ' ' . (string)($student->last_name ?? '')) ?: $student->name,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                // Avoid unique email conflicts when duplicating students (email removed in schema)
                'grade_level' => $newClassroom->grade_level,
                'section' => $newClassroom->section,
                'classroom_id' => $newClassroom->id,
                'is_active' => true,
                // Parent/guardian information
                'mother_enabled' => (bool)($student->mother_enabled ?? false),
                'mother_first_name' => $student->mother_first_name,
                'mother_last_name' => $student->mother_last_name,
                'mother_email' => $student->mother_email,
                'mother_contact' => $student->mother_contact,
                'father_enabled' => (bool)($student->father_enabled ?? false),
                'father_first_name' => $student->father_first_name,
                'father_last_name' => $student->father_last_name,
                'father_email' => $student->father_email,
                'father_contact' => $student->father_contact,
                'guardian_enabled' => (bool)($student->guardian_enabled ?? false),
                'guardian_first_name' => $student->guardian_first_name,
                'guardian_last_name' => $student->guardian_last_name,
                'guardian_email' => $student->guardian_email,
                'guardian_contact' => $student->guardian_contact,
            ]);
            $copiedCount++;
        }
        return $copiedCount;
    }

    public function getDashboardStatsProperty()
    {
        $teacherId = auth()->id();
        
        return [
            'total_classrooms' => Classroom::where('teacher_id', $teacherId)
                ->where('is_active', true)
                ->count(),
            'total_students' => Student::whereHas('classroom', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                      ->where('is_active', true);
            })
            ->where('is_active', true)
            ->count(),
            'total_subjects' => Subject::where('teacher_id', $teacherId)
                ->where('is_active', true)
                ->count(),
            'today_attendance' => Attendance::where('marked_by', $teacherId)
                                           ->where('is_active', true)
                                           ->whereDate('date', now()->toDateString())
                                           ->count(),
        ];
    }

    public function render()
    {
        return view('livewire.roles.teacher.dashboard');
    }

    // Parent access requests
    // public function getAccessRequestsForTeacherProperty() { return collect(); }

    // public function approveAccessRequest($requestId) { }

    // public function openRejectModal($requestId) { }

    // public function rejectAccessRequest() { }
}