<?php

use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app.sidebar-light')] class extends Component {
    public $students = [];
    public $selectedStudent = null;
    public $selectedSubject = null;
    public $viewMode = 'overview'; // overview, subjects, attendance, grades

    public function mount(): void
    {
        $this->students = session('matching_students', []);
        $this->students = $this->normalizeStudents($this->students);
        // Ensure session holds normalized structure to avoid stale int IDs
        session(['matching_students' => $this->students]);
        
        // If no students found and we have a logged-in user, try to find students for this user
        if (empty($this->students) && auth()->check()) {
            $this->students = $this->findMatchingStudents(auth()->user());
            $this->students = $this->normalizeStudents($this->students);
            session(['matching_students' => $this->students]);
        }
        
        // If still no students found, redirect to no-match page
        if (empty($this->students)) {
            session()->forget('matching_students'); // Clear the session
            $this->redirect(route('auth.no-match'), navigate: true);
        }
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
    }

    public function selectSubject($subjectId): void
    {
        $this->selectedSubject = $subjectId;
    }

    public function getStudentGrades($studentId)
    {
        $student = Student::find($studentId);
        return $student ? $student->grades()->with('subject')->get() : collect();
    }

    public function getStudentAttendance($studentId)
    {
        $student = Student::find($studentId);
        return $student ? $student->attendance()->latest()->take(10)->get() : collect();
    }

    public function getStudentSubjects($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) return collect();
        
        return \App\Models\Subject::where('classroom_id', $student->classroom_id)
            ->with('teacher')
            ->get();
    }

    public function getSubjectAttendance($studentId, $subjectId)
    {
        $student = Student::find($studentId);
        return $student ? $student->attendance()->where('subject_id', $subjectId)->latest()->get() : collect();
    }

    public function getSubjectGrades($studentId, $subjectId)
    {
        $student = Student::find($studentId);
        return $student ? $student->grades()->where('subject_id', $subjectId)->get() : collect();
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
}; ?>

<flux:main class="h-full">
    <div class="h-full py-6 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <!-- Header -->
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4 animate-pulse">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    {{ __('Parent Dashboard') }}
                </h1>
                <p class="mt-2 text-gray-600 text-lg">
                    {{ __('Track your children\'s academic journey with detailed insights') }}
                </p>
            </div>

            <!-- Classrooms List -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
                @foreach($classrooms as $classroom)
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 cursor-pointer hover:shadow-xl hover:scale-105 transition-all duration-300 transform {{ $selectedClassroom === $classroom['id'] ? 'ring-4 ring-blue-400 bg-gradient-to-br from-blue-50 to-indigo-50' : 'hover:bg-gradient-to-br hover:from-gray-50 hover:to-blue-50' }}"
                         wire:click="selectClassroom({{ $classroom['id'] }})">
                        <div class="flex flex-col items-center text-center space-y-4">
                            <div class="relative">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                @if($selectedClassroom === $classroom['id'])
                                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center animate-bounce">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-lg font-bold text-gray-900">
                                    {{ $classroom['display_name'] }}
                                </h3>
                                <div class="flex items-center justify-center space-x-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        {{ count($classroom['students']) }} Student(s)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Selected Classroom Details -->
            @php
                $selectedClassroomData = null;
                if ($selectedClassroom) {
                    $selectedClassroomData = collect($classrooms)->firstWhere('id', $selectedClassroom);
                }
                
                $selectedAssoc = null;
                $selectedStudentIdDetails = null;
                if (is_array($selectedStudent)) {
                    $selectedAssoc = $selectedStudent;
                    $selectedStudentIdDetails = $selectedStudent['id'] ?? null;
                } elseif ($selectedStudent instanceof \App\Models\Student) {
                    $selectedAssoc = $selectedStudent->toArray();
                    $selectedStudentIdDetails = $selectedStudent->id;
                } elseif (is_int($selectedStudent) || (is_string($selectedStudent) && ctype_digit($selectedStudent))) {
                    $model = \App\Models\Student::find((int) $selectedStudent);
                    if ($model) {
                        $selectedAssoc = $model->toArray();
                        $selectedStudentIdDetails = $model->id;
                    }
                }
            @endphp
            @if($selectedClassroomData)
                <!-- Classroom Header -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ $selectedClassroomData['display_name'] }} - Students
                        </h2>
                        @if($selectedAssoc)
                            <p class="text-sm text-gray-600 mb-4">
                                Viewing: {{ $selectedAssoc['first_name'] ?? '' }} {{ $selectedAssoc['last_name'] ?? '' }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Content Area -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="p-6">
                        @if(!$selectedAssoc)
                            <!-- Students List -->
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">Select a Student</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($selectedClassroomData['students'] as $student)
                                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 cursor-pointer hover:shadow-lg hover:scale-105 transition-all duration-300 transform"
                                             wire:click="selectStudent({{ $student['id'] }})">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-bold text-lg">
                                                        {{ substr($student['first_name'], 0, 1) }}{{ substr($student['last_name'], 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-bold text-gray-900">{{ $student['first_name'] }} {{ $student['last_name'] }}</h4>
                                                    <p class="text-sm text-gray-600">Click to view subjects</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        @elseif($viewMode === 'subjects')
                            <!-- Student Subject Details -->
                            <div>
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-2xl font-bold text-gray-900">
                                        {{ $selectedAssoc['first_name'] ?? '' }} {{ $selectedAssoc['last_name'] ?? '' }} 
                                        @if($selectedSubject)
                                            - {{ \App\Models\Subject::find($selectedSubject)->name ?? 'Selected Subject' }}
                                        @else
                                            - Subjects
                                        @endif
                                    </h3>
                                    <div class="flex space-x-2">
                                        @if($selectedSubject)
                                            <button wire:click="selectSubject(null)" 
                                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm font-medium hover:bg-gray-600 transition-colors">
                                                ← Back to Subjects
                                            </button>
                                        @endif
                                        <button wire:click="selectStudent(null)" 
                                                class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors">
                                            ← Back to Students
                                        </button>
                                    </div>
                                </div>
                                
                                @if($selectedSubject)
                                    <!-- Selected Subject Details -->
                                    @php
                                        $subject = \App\Models\Subject::with('teacher')->find($selectedSubject);
                                    @endphp
                                    @if($subject)
                                        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 mb-6">
                                            <div class="flex items-center space-x-4 mb-4">
                                                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="text-2xl font-bold text-gray-900">{{ $subject->name }}</h4>
                                                    <p class="text-lg text-gray-600">Teacher: {{ $subject->teacher->name ?? 'Not Assigned' }}</p>
                                                    @if($subject->description)
                                                        <p class="text-sm text-gray-500 mt-2">{{ $subject->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="flex space-x-4">
                                                <button wire:click="setViewMode('attendance')" 
                                                        class="px-6 py-3 bg-yellow-500 text-white rounded-lg text-sm font-medium hover:bg-yellow-600 transition-colors flex items-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                    </svg>
                                                    View Attendance
                                                </button>
                                                <button wire:click="setViewMode('grades')" 
                                                        class="px-6 py-3 bg-purple-500 text-white rounded-lg text-sm font-medium hover:bg-purple-600 transition-colors flex items-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                                    </svg>
                                                    View Grades
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <!-- All Subjects for Student -->
                                    @php
                                        $subjects = $selectedStudentIdDetails ? $this->getStudentSubjects($selectedStudentIdDetails) : collect();
                                    @endphp
                                    @if($subjects->count() > 0)
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            @foreach($subjects as $subject)
                                                <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 cursor-pointer"
                                                     wire:click="selectSubject({{ $subject->id }})">
                                                    <div class="flex items-center space-x-4 mb-4">
                                                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1">
                                                            <h4 class="font-bold text-gray-900">{{ $subject->name }}</h4>
                                                            <p class="text-sm text-gray-600">Teacher: {{ $subject->teacher->name ?? 'Not Assigned' }}</p>
                                                        </div>
                                                    </div>
                                                    <p class="text-sm text-gray-500 text-center">Click to view details</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-12">
                                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <p class="text-gray-500 text-lg">No subjects found for this student</p>
                                        </div>
                                    @endif
                                @endif
                            </div>

                        @elseif($viewMode === 'attendance')
                            <!-- Attendance Content -->
                            <div>
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $selectedAssoc['first_name'] ?? '' }} {{ $selectedAssoc['last_name'] ?? '' }} - Attendance</h3>
                                    <div class="flex space-x-2">
                                        <button wire:click="setViewMode('subjects')" 
                                                class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm font-medium hover:bg-gray-600 transition-colors">
                                            ← Back to Subjects
                                        </button>
                                        <button wire:click="selectStudent(null)" 
                                                class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors">
                                            ← Back to Students
                                        </button>
                                    </div>
                                </div>
                                
                                @php
                                    $attendance = $selectedStudentIdDetails ? 
                                        ($selectedSubject ? $this->getSubjectAttendance($selectedStudentIdDetails, $selectedSubject) : $this->getStudentAttendance($selectedStudentIdDetails)) 
                                        : collect();
                                @endphp
                                @if($attendance->count() > 0)
                                    <div class="bg-white rounded-lg shadow overflow-hidden">
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gradient-to-r from-yellow-50 to-orange-50">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach($attendance as $record)
                                                        <tr class="hover:bg-gray-50 transition-colors">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {{ $record->date->format('M d, Y') }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $record->status === 'present' ? 'bg-green-100 text-green-800' : ($record->status === 'absent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                                    {{ ucfirst($record->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $record->subject->name ?? 'N/A' }}
                                                            </td>
                                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $record->notes ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        <p class="text-gray-500 text-lg">No attendance records available</p>
                                    </div>
                                @endif
                            </div>

                        @elseif($viewMode === 'grades')
                            <!-- Grades Content -->
                            <div>
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $selectedAssoc['first_name'] ?? '' }} {{ $selectedAssoc['last_name'] ?? '' }} - Grades</h3>
                                    <div class="flex space-x-2">
                                        <button wire:click="setViewMode('subjects')" 
                                                class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm font-medium hover:bg-gray-600 transition-colors">
                                            ← Back to Subjects
                                        </button>
                                        <button wire:click="selectStudent(null)" 
                                                class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors">
                                            ← Back to Students
                                        </button>
                                    </div>
                                </div>
                                
                                @php
                                    $grades = $selectedStudentIdDetails ? 
                                        ($selectedSubject ? $this->getSubjectGrades($selectedStudentIdDetails, $selectedSubject) : $this->getStudentGrades($selectedStudentIdDetails)) 
                                        : collect();
                                @endphp
                                @if($grades->count() > 0)
                                    @php
                                        // Group grades by subject
                                        $groupedGrades = $grades->groupBy(function($grade) {
                                            return $grade->subject->name ?? 'Unknown Subject';
                                        });
                                        
                                        // Get current subject for mobile pagination
                                        $subjects = $groupedGrades->keys()->toArray();
                                        $currentSubject = $subjects[$currentGradePage - 1] ?? null;
                                        $totalSubjects = count($subjects);
                                    @endphp
                                    
                                    <!-- Desktop View -->
                                    <div class="hidden md:block space-y-8">
                                        @foreach($groupedGrades as $subjectName => $subjectGrades)
                                            <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 transition-all duration-300">
                                                <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                                    <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                    {{ $subjectName }}
                                                </h4>
                                                
                                                <!-- Grades for this subject -->
                                                <div class="grid grid-cols-4 gap-4">
                                                    @php
                                                        // Get grades for each quarter (1st to 4th)
                                                        $quarterGrades = [
                                                            '1st Quarter' => $subjectGrades->where('term', '1st Quarter')->first(),
                                                            '2nd Quarter' => $subjectGrades->where('term', '2nd Quarter')->first(),
                                                            '3rd Quarter' => $subjectGrades->where('term', '3rd Quarter')->first(),
                                                            '4th Quarter' => $subjectGrades->where('term', '4th Quarter')->first(),
                                                        ];
                                                    @endphp
                                                    
                                                    @foreach($quarterGrades as $quarter => $grade)
                                                        <div class="bg-white rounded-lg p-4 text-center shadow-sm border border-purple-200 hover:shadow-md transition-all duration-200">
                                                            <div class="text-sm font-medium text-gray-600 mb-2">{{ $quarter }}</div>
                                                            <div class="text-2xl font-bold text-purple-600">
                                                                {{ $grade ? ($grade->score ?? $grade->grade ?? 'N/A') : 'N/A' }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                
                                                @if(!$loop->last)
                                                    <hr class="mt-6 border-purple-200">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Mobile View with Pagination -->
                                    <div class="md:hidden">
                                        @if($currentSubject && isset($groupedGrades[$currentSubject]))
                                            @php
                                                $subjectGrades = $groupedGrades[$currentSubject];
                                            @endphp
                                            <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 transition-all duration-500 transform">
                                                <h4 class="text-xl font-bold text-gray-900 mb-6 text-center">
                                                    {{ $currentSubject }}
                                                </h4>
                                                
                                                <!-- Mobile Grades Display -->
                                                <div class="space-y-4">
                                                    @php
                                                        // Get grades for each quarter (1st to 4th)
                                                        $quarterGrades = [
                                                            '1st Quarter' => $subjectGrades->where('term', '1st Quarter')->first(),
                                                            '2nd Quarter' => $subjectGrades->where('term', '2nd Quarter')->first(),
                                                            '3rd Quarter' => $subjectGrades->where('term', '3rd Quarter')->first(),
                                                            '4th Quarter' => $subjectGrades->where('term', '4th Quarter')->first(),
                                                        ];
                                                    @endphp
                                                    
                                                    @foreach($quarterGrades as $quarter => $grade)
                                                        <div class="bg-white rounded-lg p-4 text-center shadow-sm border border-purple-200">
                                                            <div class="text-sm font-medium text-gray-600 mb-2">{{ $quarter }}</div>
                                                            <div class="text-2xl font-bold text-purple-600">
                                                                {{ $grade ? ($grade->score ?? $grade->grade ?? 'N/A') : 'N/A' }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            
                                            <!-- Pagination Controls -->
                                            <div class="flex justify-between items-center mt-6">
                                                <button wire:click="previousGradePage" 
                                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-200 {{ $currentGradePage <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300' }}"
                                                        {{ $currentGradePage <= 1 ? 'disabled' : '' }}>
                                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                    </svg>
                                                    Previous
                                                </button>
                                                
                                                <span class="text-sm text-gray-600">
                                                    {{ $currentGradePage }} of {{ $totalSubjects }}
                                                </span>
                                                
                                                <button wire:click="nextGradePage" 
                                                        class="px-4 py-2 bg-purple-500 text-white rounded-lg font-medium transition-all duration-200 {{ $currentGradePage >= $totalSubjects ? 'opacity-50 cursor-not-allowed' : 'hover:bg-purple-600' }}"
                                                        {{ $currentGradePage >= $totalSubjects ? 'disabled' : '' }}>
                                                    Next
                                                    <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                </div>
                            @else
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                        <p class="text-gray-500 text-lg">No grades available</p>
                                    </div>
                            @endif
                        </div>
                        @else
                            <!-- Default view when no student is selected - Show Subjects and Students -->
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">Classroom Subjects & Students</h3>
                                
                                <!-- Subjects grouped by teacher -->
                                @php
                                    $subjects = collect($selectedClassroomData['subjects'] ?? []);
                                    $subjectsByTeacher = $subjects->groupBy(function($subject) {
                                        return $subject->teacher->name ?? 'Unassigned';
                                    });
                                @endphp
                                
                                @if($subjectsByTeacher->count() > 0)
                                    <div class="mb-8">
                                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Subjects by Teacher</h4>
                                        <div class="space-y-6">
                                            @foreach($subjectsByTeacher as $teacherName => $teacherSubjects)
                                                <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6">
                                                    <h5 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        {{ $teacherName }}
                                                    </h5>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                        @foreach($teacherSubjects as $subject)
                                                            <div class="bg-white rounded-lg p-4 hover:shadow-lg transition-all duration-300 cursor-pointer {{ $selectedSubject == $subject->id ? 'ring-4 ring-green-400 bg-green-50' : 'hover:bg-gray-50' }}"
                                                                 wire:click="selectSubject({{ $subject->id }})">
                                                                <div class="flex items-center space-x-3">
                                                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                                        </svg>
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <h6 class="font-semibold text-gray-900">{{ $subject->name }}</h6>
                                                                        @if($subject->description)
                                                                            <p class="text-xs text-gray-600 mt-1">{{ $subject->description }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                @if($selectedSubject == $subject->id)
                                                                    <div class="mt-3 pt-3 border-t border-green-200">
                                                                        <p class="text-sm text-gray-600 text-center">Click a student below to view grades/attendance</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Students List -->
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-700 mb-4">Students in this Classroom</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($selectedClassroomData['students'] as $student)
                                            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 cursor-pointer hover:shadow-lg hover:scale-105 transition-all duration-300 transform {{ $selectedSubject ? 'hover:ring-4 hover:ring-blue-400' : '' }}"
                                                 wire:click="selectStudent({{ $student['id'] }})">
                                                <div class="flex items-center space-x-4">
                                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-bold text-lg">
                                                            {{ substr($student['first_name'], 0, 1) }}{{ substr($student['last_name'], 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h4 class="font-bold text-gray-900">{{ $student['first_name'] }} {{ $student['last_name'] }}</h4>
                                                        @if($selectedSubject)
                                                            <p class="text-sm text-gray-600">Click to view {{ $selectedSubject ? 'subject details' : 'subjects' }}</p>
                                                        @else
                                                            <p class="text-sm text-gray-600">Click to view subjects</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 text-center py-16">
                    <div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-6 animate-pulse">
                        <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Select a Classroom</h3>
                    <p class="text-gray-600 text-lg max-w-md mx-auto">Choose a classroom from the cards above to view your children and their academic progress.</p>
                </div>
            @endif
        </div>
    </div>
</flux:main>