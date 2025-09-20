<!-- Subjects View -->
@if($currentView === 'subjects')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(!$selectedClassroomForSubjects)
    <!-- Classroom Selection -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Select Classroom to Manage Subjects</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->classrooms as $classroom)
                <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" 
                     wire:click="selectClassroomForSubjects({{ $classroom->id }})">
                    <h3 class="font-medium text-gray-900">{{ $classroom->name }}</h3>
                    <p class="text-sm text-gray-500">Grade {{ $classroom->grade_level }} - {{ $classroom->section }}</p>
                    <p class="text-sm text-gray-500">{{ $classroom->students->count() }} students</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <!-- Subject Management for Selected Classroom -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Subjects - {{ $this->classrooms->where('id', $selectedClassroomForSubjects)->first()->name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Grade {{ $this->classrooms->where('id', $selectedClassroomForSubjects)->first()->grade_level }} - 
                        {{ $this->classrooms->where('id', $selectedClassroomForSubjects)->first()->section }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="showSubjectModal()" 
                            class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                        Add Subject
                    </button>
                    <button wire:click="$set('selectedClassroomForSubjects', null)" 
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Back to Classroom Subjects Selection
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($this->subjects as $subject)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $subject->name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($subject->description, 50) }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button wire:click="showSubjectModal({{ $subject->id }})" 
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button wire:click="selectSubjectForGrades({{ $subject->id }})" 
                                    class="text-blue-600 hover:text-blue-900 mr-3">Record Grades</button>
                            <button wire:click="deleteSubject({{ $subject->id }})" 
                                    class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if(method_exists($this->subjects, 'links'))
        <div class="px-6 py-4">
            {{ $this->subjects->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
@endif

<!-- Student Grades View for Subject -->
@if($currentView === 'student-grades')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Student Grades - {{ $this->subjects->where('id', $selectedSubject)->first()->name ?? 'Subject' }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ $this->subjects->where('id', $selectedSubject)->first()->classroom->name ?? 'Classroom' }}
                    </p>
                </div>
                <button wire:click="$set('selectedSubject', null)" 
                        class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Back to Subject List
                </button>
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
                    @foreach($this->studentsForSubject as $student)
                    @php
                        $grades = $student->grades()->where('subject_id', $selectedSubject)->get();
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
                            <input type="number" 
                                   min="0" 
                                   max="100" 
                                   value="{{ $q1?->score ?? '' }}"
                                   wire:change="saveStudentGrade({{ $student->id }}, $event.target.value, '1st Quarter')"
                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" 
                                   min="0" 
                                   max="100" 
                                   value="{{ $q2?->score ?? '' }}"
                                   wire:change="saveStudentGrade({{ $student->id }}, $event.target.value, '2nd Quarter')"
                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" 
                                   min="0" 
                                   max="100" 
                                   value="{{ $q3?->score ?? '' }}"
                                   wire:change="saveStudentGrade({{ $student->id }}, $event.target.value, '3rd Quarter')"
                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" 
                                   min="0" 
                                   max="100" 
                                   value="{{ $q4?->score ?? '' }}"
                                   wire:change="saveStudentGrade({{ $student->id }}, $event.target.value, '4th Quarter')"
                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
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
        
        @if(method_exists($this->studentsForSubject, 'links'))
        <div class="px-6 py-4">
            {{ $this->studentsForSubject->links() }}
        </div>
        @endif
    </div>
</div>
@endif

<!-- Student Subjects View -->
@if($currentView === 'student-subjects')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Student Subjects - {{ $this->students->where('id', $selectedStudent)->first()->name ?? 'Student' }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ $this->students->where('id', $selectedStudent)->first()->classroom->name ?? 'Classroom' }}
                    </p>
                </div>
                <button wire:click="backToClassrooms" 
                        class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Back to Students
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($this->studentSubjects as $subject)
                    @php
                        $q1 = $subject->grades->where('term', '1st Quarter')->first();
                        $q2 = $subject->grades->where('term', '2nd Quarter')->first();
                        $q3 = $subject->grades->where('term', '3rd Quarter')->first();
                        $q4 = $subject->grades->where('term', '4th Quarter')->first();
                        $average = collect([$q1?->score, $q2?->score, $q3?->score, $q4?->score])->filter()->avg();
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $subject->name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($subject->description, 50) }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" 
                                   min="0" 
                                   max="100" 
                                   value="{{ $q1?->score ?? '' }}"
                                   wire:change="saveStudentGrade({{ $selectedStudent }}, $event.target.value, '1st Quarter', {{ $subject->id }})"
                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" 
                                   min="0" 
                                   max="100" 
                                   value="{{ $q2?->score ?? '' }}"
                                   wire:change="saveStudentGrade({{ $selectedStudent }}, $event.target.value, '2nd Quarter', {{ $subject->id }})"
                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" 
                                   min="0" 
                                   max="100" 
                                   value="{{ $q3?->score ?? '' }}"
                                   wire:change="saveStudentGrade({{ $selectedStudent }}, $event.target.value, '3rd Quarter', {{ $subject->id }})"
                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" 
                                   min="0" 
                                   max="100" 
                                   value="{{ $q4?->score ?? '' }}"
                                   wire:change="saveStudentGrade({{ $selectedStudent }}, $event.target.value, '4th Quarter', {{ $subject->id }})"
                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
