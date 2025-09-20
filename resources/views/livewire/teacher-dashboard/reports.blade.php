<!-- Reports View -->
@if($currentView === 'reports')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(!$selectedClassroomForReports)
    <!-- Classroom Selection -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Select Classroom to Generate Reports</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->classrooms as $classroom)
                <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" 
                     wire:click="selectClassroomForReports({{ $classroom->id }})">
                    <h3 class="font-medium text-gray-900">{{ $classroom->name }}</h3>
                    <p class="text-sm text-gray-500">Grade {{ $classroom->grade_level }} - {{ $classroom->section }}</p>
                    <p class="text-sm text-gray-500">{{ $classroom->students->count() }} students</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <!-- Report Generation for Selected Classroom -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    @php
                        $selectedClassroom = \App\Models\Classroom::find($selectedClassroomForReports);
                    @endphp
                    <h2 class="text-xl font-semibold text-gray-800">
                        Generate Reports - {{ $selectedClassroom?->name ?? 'Classroom' }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Grade {{ $selectedClassroom?->grade_level ?? '' }}
                        @if($selectedClassroom?->section)
                            - {{ $selectedClassroom->section }}
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Search students..." 
                           class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
                    <button wire:click="setView('subjects')" 
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Back to Subject List
                    </button>
                    <button wire:click="setView('reports')" 
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Back to Classrooms
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subjects</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Average Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($this->filteredStudentsForReports as $student)
                    @php
                        // Get all subjects for this teacher in the same grade level and section
                        $classroom = \App\Models\Classroom::find($selectedClassroomForReports);
                        $allSubjects = \App\Models\Subject::where('teacher_id', auth()->id())
                            ->whereHas('classroom', function($query) use ($classroom) {
                                $query->where('grade_level', $classroom->grade_level)
                                      ->where('section', $classroom->section)
                                      ->where('is_active', true);
                            })
                            ->where('is_active', true)
                            ->get();
                            
                        $totalScore = 0;
                        $subjectCount = max(1, $allSubjects->count());
                        foreach($allSubjects as $subject) {
                            $subjectGrades = $subject->grades()
                                ->where('student_id', $student->id)
                                ->where('is_active', true)
                                ->get();
                            $avg = $subjectGrades->count() > 0 ? $subjectGrades->avg('score') : 0;
                            $totalScore += $avg;
                        }
                        $averageGrade = $totalScore / $subjectCount;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $student->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $allSubjects->count() }} subjects
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="font-medium {{ $averageGrade >= 75 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $averageGrade > 0 ? number_format($averageGrade, 1) : 'No grades' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button wire:click="generateStudentReport({{ $student->id }})" 
                                    class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 text-sm">
                                Generate Report Card
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Generated Report Cards -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Generated Report Cards</h3>
            <p class="text-sm text-gray-500 mt-1">View and manage generated report cards for this classroom</p>
        </div>
        
        @if($this->reportCards->count() > 0)
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($this->reportCards as $reportCard)
                <div class="border-2 border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-gradient-to-br from-blue-50 to-white">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">{{ $reportCard->student->name }}</h4>
                        <p class="text-sm text-gray-600">Grade {{ $reportCard->classroom->grade_level }} - {{ $reportCard->classroom->section }}</p>
                    </div>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">School Year:</span>
                            <span class="font-medium">{{ $reportCard->school_year }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Quarter:</span>
                            <span class="font-medium">{{ $reportCard->semester }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Average:</span>
                            <span class="font-bold text-lg {{ $reportCard->average >= 75 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $reportCard->average }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-2 py-1 text-xs rounded-full {{ $reportCard->remarks === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $reportCard->remarks }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                            <button wire:click="exportReportCard({{ $reportCard->id }})" 
                                class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 text-sm font-medium">
                            Export PDF
                        </button>
                            <button wire:click="deleteReportCard({{ $reportCard->id }})" 
                                class="px-3 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md text-sm"
                                onclick="return confirm('Are you sure you want to delete this report card?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mt-3 text-xs text-gray-500 text-center">
                        Generated on {{ $reportCard->created_at->format('M d, Y g:i A') }}
                    </div>
                </div>
                    @endforeach
            </div>
        </div>
        
        @if(method_exists($this->reportCards, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $this->reportCards->links() }}
        </div>
        @endif
        @else
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Report Cards Generated</h3>
            <p class="text-gray-500 mb-4">Generate report cards for students using the "Generate Report Card" button above.</p>
        </div>
        @endif
    </div>
    @endif
</div>
@endif

<!-- Student Grades - Subject View -->
@if($currentView === 'subject-grades')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Student Grades - {{ $this->subjects->where('id', $selectedSubjectForGrades)->first()->name ?? 'Subject' }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ $this->subjects->where('id', $selectedSubjectForGrades)->first()->classroom->name ?? 'Classroom' }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="setView('subjects')" 
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Back to Subject List
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">1st Quarter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">2nd Quarter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">3rd Quarter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">4th Quarter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Average</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($this->studentsForSubjectGrades as $student)
                    @php
                        $grades = $student->grades()->where('subject_id', $selectedSubjectForGrades)->get();
                        $q1 = $grades->where('term', '1st Quarter')->first();
                        $q2 = $grades->where('term', '2nd Quarter')->first();
                        $q3 = $grades->where('term', '3rd Quarter')->first();
                        $q4 = $grades->where('term', '4th Quarter')->first();
                        $average = collect([$q1?->score, $q2?->score, $q3?->score, $q4?->score])->filter()->avg();
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text"
                                   x-data
                                   x-on:change="let v=parseFloat($event.target.value); if (isNaN(v)) { $event.target.value=''; return; } v=Math.min(99.99, Math.max(60, v)); $event.target.value=v.toFixed(2); $wire.saveStudentGrade({{ $student->id }}, $event.target.value, '1st Quarter', {{ $selectedSubjectForGrades }});"
                                   value="{{ $q1?->score ?? '' }}"
                                   placeholder="60.00"
                                   class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-2">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text"
                                   x-data
                                   x-on:change="let v=parseFloat($event.target.value); if (isNaN(v)) { $event.target.value=''; return; } v=Math.min(99.99, Math.max(60, v)); $event.target.value=v.toFixed(2); $wire.saveStudentGrade({{ $student->id }}, $event.target.value, '2nd Quarter', {{ $selectedSubjectForGrades }});"
                                   value="{{ $q2?->score ?? '' }}"
                                   placeholder="60.00"
                                   class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-2">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text"
                                   x-data
                                   x-on:change="let v=parseFloat($event.target.value); if (isNaN(v)) { $event.target.value=''; return; } v=Math.min(99.99, Math.max(60, v)); $event.target.value=v.toFixed(2); $wire.saveStudentGrade({{ $student->id }}, $event.target.value, '3rd Quarter', {{ $selectedSubjectForGrades }});"
                                   value="{{ $q3?->score ?? '' }}"
                                   placeholder="60.00"
                                   class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-2">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text"
                                   x-data
                                   x-on:change="let v=parseFloat($event.target.value); if (isNaN(v)) { $event.target.value=''; return; } v=Math.min(99.99, Math.max(60, v)); $event.target.value=v.toFixed(2); $wire.saveStudentGrade({{ $student->id }}, $event.target.value, '4th Quarter', {{ $selectedSubjectForGrades }});"
                                   value="{{ $q4?->score ?? '' }}"
                                   placeholder="60.00"
                                   class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-2">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">
                                {{ $average ? number_format($average, 1) : '-' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if(method_exists($this->studentsForSubjectGrades, 'links'))
        <div class="px-6 py-4">
            {{ $this->studentsForSubjectGrades->links() }}
        </div>
        @endif
    </div>
</div>
@endif
