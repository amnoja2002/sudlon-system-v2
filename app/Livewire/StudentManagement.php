<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\Grade;
use App\Models\ReportCard;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
class StudentManagement extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public $search = '';
    public $filterGradeLevel = '';
    public $filterSection = '';
    public $filterClassroom = '';

    // Modal States
    public $showingStudentModal = false;
    public $showingGradeModal = false;
    public $showingReportCardModal = false;
    public $showingDeleteModal = false;

    // Selected Items
    public $selectedStudent = null;
    public $selectedGrade = null;
    public $selectedReportCard = null;
    public $deleteStudentId = null;

    // Form Data
    public $studentData = [
        'name' => '',
        'email' => '',
        'grade_level' => '',
        'section' => '',
        'classroom_id' => null,
    ];

    public $gradeData = [
        'student_id' => null,
        'subject_id' => null,
        'subject' => '',
        'term' => '',
        'score' => '',
    ];

    public $reportCardData = [
        'student_id' => null,
        'classroom_id' => null,
        'school_year' => '',
        'semester' => '',
        'grades' => [],
        'average' => '',
        'remarks' => '',
        'teacher_comments' => '',
    ];

    // Collections
    public $classrooms;
    public $subjects;
    public $students;

    public function mount()
    {
        $this->classrooms = Classroom::where('is_active', true)->get();
        $this->subjects = Subject::where('is_active', true)->get();
        $this->students = Student::where('is_active', true)->get();
    }

    // Student CRUD Operations
    public function showStudentModal($studentId = null)
    {
        $this->selectedStudent = $studentId ? Student::find($studentId) : null;
        $this->showingStudentModal = true;
        
        if ($this->selectedStudent) {
            $this->studentData = [
                'name' => $this->selectedStudent->name,
                'email' => $this->selectedStudent->email,
                'grade_level' => $this->selectedStudent->grade_level,
                'section' => $this->selectedStudent->section,
                'classroom_id' => $this->selectedStudent->classroom_id,
            ];
        } else {
            $this->reset('studentData');
        }
    }

    public function createStudent()
    {
        $this->validate([
            'studentData.name' => 'required|min:3',
            'studentData.email' => 'required|email|unique:students,email',
            'studentData.grade_level' => 'required',
            'studentData.section' => 'required',
            'studentData.classroom_id' => 'required|exists:classrooms,id',
        ]);

        Student::create([
            ...$this->studentData,
            'is_active' => true,
        ]);

        $this->showingStudentModal = false;
        $this->reset('studentData');
        session()->flash('message', 'Student created successfully.');
    }

    public function updateStudent()
    {
        $this->validate([
            'studentData.name' => 'required|min:3',
            'studentData.email' => 'required|email|unique:students,email,' . $this->selectedStudent->id,
            'studentData.grade_level' => 'required',
            'studentData.section' => 'required',
            'studentData.classroom_id' => 'required|exists:classrooms,id',
        ]);

        $this->selectedStudent->update($this->studentData);
        $this->showingStudentModal = false;
        session()->flash('message', 'Student updated successfully.');
    }

    public function confirmDeleteStudent($studentId)
    {
        $this->deleteStudentId = $studentId;
        $this->showingDeleteModal = true;
    }

    public function deleteStudent()
    {
        if ($this->deleteStudentId) {
            $student = Student::find($this->deleteStudentId);
            if ($student) {
                $student->update(['is_active' => false]);
                session()->flash('message', 'Student deactivated successfully.');
            }
        }
        $this->showingDeleteModal = false;
        $this->deleteStudentId = null;
    }

    // Grade Management
    public function showGradeModal($studentId, $gradeId = null)
    {
        $this->selectedStudent = Student::find($studentId);
        $this->selectedGrade = $gradeId ? Grade::find($gradeId) : null;
        $this->showingGradeModal = true;
        
        if ($this->selectedGrade) {
            $this->gradeData = [
                'student_id' => $this->selectedGrade->student_id,
                'subject_id' => $this->selectedGrade->subject_id,
                'subject' => $this->selectedGrade->subject,
                'term' => $this->selectedGrade->term,
                'score' => $this->selectedGrade->score,
            ];
        } else {
            $this->gradeData = [
                'student_id' => $studentId,
                'subject_id' => null,
                'subject' => '',
                'term' => '',
                'score' => '',
            ];
        }
    }

    public function saveGrade()
    {
        $this->validate([
            'gradeData.student_id' => 'required|exists:students,id',
            'gradeData.subject_id' => 'required|exists:subjects,id',
            'gradeData.subject' => 'required',
            'gradeData.term' => 'required',
            'gradeData.score' => 'required|numeric|min:0|max:100',
        ]);

        if ($this->selectedGrade) {
            $this->selectedGrade->update($this->gradeData);
            session()->flash('message', 'Grade updated successfully.');
        } else {
            Grade::create([
                ...$this->gradeData,
                'is_active' => true,
            ]);
            session()->flash('message', 'Grade added successfully.');
        }

        $this->showingGradeModal = false;
        $this->reset('gradeData');
    }

    public function deleteGrade($gradeId)
    {
        $grade = Grade::find($gradeId);
        if ($grade) {
            $grade->update(['is_active' => false]);
            session()->flash('message', 'Grade deleted successfully.');
        }
    }

    // Report Card Management
    public function showReportCardModal($studentId, $reportCardId = null)
    {
        $this->selectedStudent = Student::find($studentId);
        $this->selectedReportCard = $reportCardId ? ReportCard::find($reportCardId) : null;
        $this->showingReportCardModal = true;
        
        if ($this->selectedReportCard) {
            $this->reportCardData = [
                'student_id' => $this->selectedReportCard->student_id,
                'classroom_id' => $this->selectedReportCard->classroom_id,
                'school_year' => $this->selectedReportCard->school_year,
                'semester' => $this->selectedReportCard->semester,
                'grades' => $this->selectedReportCard->grades ?? [],
                'average' => $this->selectedReportCard->average,
                'remarks' => $this->selectedReportCard->remarks,
                'teacher_comments' => $this->selectedReportCard->teacher_comments,
            ];
        } else {
            $this->reportCardData = [
                'student_id' => $studentId,
                'classroom_id' => $this->selectedStudent->classroom_id,
                'school_year' => date('Y') . '-' . (date('Y') + 1),
                'semester' => '1st',
                'grades' => [],
                'average' => '',
                'remarks' => '',
                'teacher_comments' => '',
            ];
        }
    }

    public function saveReportCard()
    {
        $this->validate([
            'reportCardData.student_id' => 'required|exists:students,id',
            'reportCardData.classroom_id' => 'required|exists:classrooms,id',
            'reportCardData.school_year' => 'required',
            'reportCardData.semester' => 'required',
            'reportCardData.average' => 'required|numeric|min:0|max:100',
            'reportCardData.remarks' => 'required',
        ]);

        $data = [
            ...$this->reportCardData,
            'generated_by' => auth()->id(),
            'generated_at' => now(),
            'is_active' => true,
        ];

        if ($this->selectedReportCard) {
            $this->selectedReportCard->update($data);
            session()->flash('message', 'Report card updated successfully.');
        } else {
            ReportCard::create($data);
            session()->flash('message', 'Report card created successfully.');
        }

        $this->showingReportCardModal = false;
        $this->reset('reportCardData');
    }

    // Export Functions
    public function exportStudentData()
    {
        $students = $this->getFilteredStudents();
        
        $pdf = Pdf::loadView('pdf.student-data', [
            'students' => $students,
            'filters' => [
                'search' => $this->search,
                'grade_level' => $this->filterGradeLevel,
                'section' => $this->filterSection,
                'classroom' => $this->filterClassroom,
            ]
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'student-data-' . date('Y-m-d') . '.pdf');
    }

    public function exportReportCard($studentId)
    {
        $student = Student::with(['classroom', 'reportCards' => function($query) {
            $query->where('is_active', true)->latest()->first();
        }])->find($studentId);
        
        if (!$student || !$student->reportCards->first()) {
            session()->flash('error', 'No report card found for this student.');
            return;
        }
        
        $reportCard = $student->reportCards->first();
        
        $pdf = Pdf::loadView('pdf.export-report-card', [
            'student' => $student,
            'reportCard' => $reportCard
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'report-card-' . $student->name . '-' . date('Y-m-d') . '.pdf');
    }

    // Computed Properties
    public function getFilteredStudentsProperty()
    {
        return Student::with(['classroom', 'grades' => function($query) {
            $query->where('is_active', true);
        }])
        ->where('is_active', true)
        ->when($this->search, function($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->when($this->filterGradeLevel, function($query) {
            $query->where('grade_level', $this->filterGradeLevel);
        })
        ->when($this->filterSection, function($query) {
            $query->where('section', $this->filterSection);
        })
        ->when($this->filterClassroom, function($query) {
            $query->where('classroom_id', $this->filterClassroom);
        })
        ->paginate(10);
    }

    public function getFilteredStudents()
    {
        return Student::with(['classroom', 'grades' => function($query) {
            $query->where('is_active', true);
        }])
        ->where('is_active', true)
        ->when($this->search, function($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->when($this->filterGradeLevel, function($query) {
            $query->where('grade_level', $this->filterGradeLevel);
        })
        ->when($this->filterSection, function($query) {
            $query->where('section', $this->filterSection);
        })
        ->when($this->filterClassroom, function($query) {
            $query->where('classroom_id', $this->filterClassroom);
        })
        ->get();
    }

    public function getGradeLevelsProperty()
    {
        return Student::where('is_active', true)
            ->distinct()
            ->pluck('grade_level')
            ->sort()
            ->values();
    }

    public function getSectionsProperty()
    {
        return Student::where('is_active', true)
            ->distinct()
            ->pluck('section')
            ->sort()
            ->values();
    }

    public function render()
    {
        return view('livewire.student-management');
    }
}
