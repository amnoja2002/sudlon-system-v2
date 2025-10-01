{{-- this is for principal management of student --}}

<div class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Student Management</h1>
                <div class="flex items-center gap-3">
                    <button wire:click="exportStudentData" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        Export Student Data
                    </button>
                    <button wire:click="showStudentModal" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Add New Student
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="p-6">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Bar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search by Name</label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search students..."
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Grade Level Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                    <select wire:model.live="filterGradeLevel" 
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Grades</option>
                        @foreach($this->gradeLevels as $grade)
                            <option value="{{ $grade }}">Grade {{ $grade }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Section Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Section</label>
                    <select wire:model.live="filterSection" 
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Sections</option>
                        @foreach($this->sections as $section)
                            <option value="{{ $section }}">{{ $section }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Classroom Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Classroom</label>
                    <select wire:model.live="filterClassroom" 
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Classrooms</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Grade & Section</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Grades</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($this->filteredStudents as $student)
                        <tr wire:key="student-{{ $student->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    Grade {{ $student->grade_level }} - {{ $student->section }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($student->grades->take(3) as $grade)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            {{ $grade->subject }}: {{ $grade->score }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-500">No grades</span>
                                    @endforelse
                                    @if($student->grades->count() > 3)
                                        <span class="text-xs text-gray-500">+{{ $student->grades->count() - 3 }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button wire:click="showGradeModal({{ $student->id }})" 
                                            class="text-blue-600 hover:text-blue-900">
                                        Manage Grades
                                    </button>
                                    <button wire:click="showReportCardModal({{ $student->id }})" 
                                            class="text-green-600 hover:text-green-900">
                                        Report Card
                                    </button>
                                    <button wire:click="exportReportCard({{ $student->id }})" 
                                            class="text-purple-600 hover:text-purple-900">
                                        Export
                                    </button>
                                    <button wire:click="showStudentModal({{ $student->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </button>
                                    <button wire:click="confirmDeleteStudent({{ $student->id }})" 
                                            class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No students found matching your criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4">
                {{ $this->filteredStudents->links() }}
            </div>
        </div>
    </div>

    <!-- Student Modal -->
    <div x-data="{ show: @entangle('showingStudentModal').live }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-[70] overflow-y-auto"
         x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div x-show="show" class="fixed inset-0 bg-black/50 transition-opacity"></div>
            
            <div x-show="show" class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $selectedStudent ? 'Edit Student' : 'Add New Student' }}
                    </h3>
                    
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" wire:model="studentData.name" 
                                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                            @error('studentData.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" wire:model="studentData.email" 
                                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                            @error('studentData.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Grade Level</label>
                            <select wire:model="studentData.grade_level" 
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Grade Level</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">Grade {{ $i }}</option>
                                @endfor
                            </select>
                            @error('studentData.grade_level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Section</label>
                            <input type="text" wire:model="studentData.section" 
                                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                            @error('studentData.section') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Classroom</label>
                            <select wire:model="studentData.classroom_id" 
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Classroom</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                                @endforeach
                            </select>
                            @error('studentData.classroom_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    @if($selectedStudent)
                        <button wire:click="updateStudent" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update Student
                        </button>
                    @else
                        <button wire:click="createStudent" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Create Student
                        </button>
                    @endif
                    <button wire:click="$set('showingStudentModal', false)" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Management Modal -->
    <div x-data="{ show: @entangle('showingGradeModal').live }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-[70] overflow-y-auto"
         x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div x-show="show" class="fixed inset-0 bg-black/50 transition-opacity"></div>
            
            <div x-show="show" class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Manage Grades - {{ $selectedStudent?->name }}
                    </h3>
                    
                    <!-- Add New Grade Form -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-md font-medium text-gray-800 mb-3">Add/Edit Grade</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Subject</label>
                                <select wire:model="gradeData.subject_id" 
                                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                @error('gradeData.subject_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Subject Name</label>
                                <input type="text" wire:model="gradeData.subject" 
                                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                                @error('gradeData.subject') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Term</label>
                                <select wire:model="gradeData.term" 
                                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Term</option>
                                    <option value="1st Quarter">1st Quarter</option>
                                    <option value="2nd Quarter">2nd Quarter</option>
                                    <option value="3rd Quarter">3rd Quarter</option>
                                    <option value="4th Quarter">4th Quarter</option>
                                    <option value="Final">Final</option>
                                </select>
                                @error('gradeData.term') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Score</label>
                                <input type="number" wire:model="gradeData.score" min="0" max="100" step="0.01"
                                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                                @error('gradeData.score') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4 flex justify-end">
                            <button wire:click="saveGrade" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                {{ $selectedGrade ? 'Update Grade' : 'Add Grade' }}
                            </button>
                        </div>
                    </div>
                    
                    <!-- Existing Grades List -->
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-800 mb-3">Existing Grades</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Subject</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Term</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Score</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($selectedStudent?->grades ?? [] as $grade)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $grade->subject }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $grade->term }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $grade->score }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                                            <button wire:click="showGradeModal({{ $selectedStudent->id }}, {{ $grade->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 mr-2">Edit</button>
                                            <button wire:click="deleteGrade({{ $grade->id }})" 
                                                    class="text-red-600 hover:text-red-900">Delete</button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-2 text-center text-gray-500 text-sm">No grades found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="$set('showingGradeModal', false)" 
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Card Modal -->
    <div x-data="{ show: @entangle('showingReportCardModal').live }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-[70] overflow-y-auto"
         x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div x-show="show" class="fixed inset-0 bg-black/50 transition-opacity"></div>
            
            <div x-show="show" class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Report Card - {{ $selectedStudent?->name }}
                    </h3>
                    
                    <div class="mt-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">School Year</label>
                                <input type="text" wire:model="reportCardData.school_year" 
                                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                                @error('reportCardData.school_year') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semester</label>
                                <select wire:model="reportCardData.semester" 
                                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Semester</option>
                                    <option value="1st">1st Semester</option>
                                    <option value="2nd">2nd Semester</option>
                                </select>
                                @error('reportCardData.semester') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Average Grade</label>
                                <input type="number" wire:model="reportCardData.average" min="0" max="100" step="0.01"
                                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                                @error('reportCardData.average') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Remarks</label>
                                <select wire:model="reportCardData.remarks" 
                                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Remarks</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Very Good">Very Good</option>
                                    <option value="Good">Good</option>
                                    <option value="Fair">Fair</option>
                                    <option value="Needs Improvement">Needs Improvement</option>
                                </select>
                                @error('reportCardData.remarks') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teacher Comments</label>
                            <textarea wire:model="reportCardData.teacher_comments" rows="3"
                                      class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="saveReportCard" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ $selectedReportCard ? 'Update Report Card' : 'Create Report Card' }}
                    </button>
                    <button wire:click="$set('showingReportCardModal', false)" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ show: @entangle('showingDeleteModal').live }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-[80]">
        <div class="absolute inset-0 bg-black/50" @click="$wire.set('showingDeleteModal', false)"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Confirm Deactivation</h3>
                    <p class="mt-2 text-sm text-gray-600">Are you sure you want to deactivate this student? This action cannot be undone.</p>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="deleteStudent" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Deactivate Student
                    </button>
                    <button wire:click="$set('showingDeleteModal', false)" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-4 right-4 z-[90] bg-green-500 text-white px-6 py-3 rounded-md shadow-lg">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-4 right-4 z-[90] bg-red-500 text-white px-6 py-3 rounded-md shadow-lg">
            {{ session('error') }}
        </div>
    @endif
</div>
