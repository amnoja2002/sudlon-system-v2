<div class="bg-gray-100 min-h-screen p-4">
    <!-- Notifications -->
    <div class="mb-4 flex items-center justify-end" x-data="{ open: @entangle('showRequests') }" x-cloak>
        <button @click="open = true" class="relative inline-flex items-center justify-center rounded-full h-10 w-10 bg-white border border-gray-200 shadow hover:bg-gray-50">
            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            @if($this->accessRequests->count() > 0)
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center rounded-full bg-red-600 text-white text-xs h-5 min-w-[1.25rem] px-1">{{ $this->accessRequests->count() }}</span>
            @endif
        </button>
    </div>

    @if($this->accessRequests->isNotEmpty())
        <div x-data="{ open: @entangle('showRequests') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-xl mx-4">
                <div class="flex items-center justify-between px-4 py-3 border-b">
                    <h3 class="text-sm font-medium text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Requests ({{ $this->accessRequests->count() }})
                    </h3>
                    <button @click="open=false" class="text-sm text-gray-500 hover:text-gray-700">Close</button>
                </div>
                <div class="p-3 max-h-[70vh] overflow-y-auto space-y-2">
                    @foreach($this->accessRequests as $req)
                        <div class="rounded-md p-3 text-sm
                            @if($req->status === 'approved') bg-green-50 text-green-800 border border-green-200
                            @elseif($req->status === 'rejected') bg-red-50 text-red-800 border border-red-200
                            @else bg-yellow-50 text-yellow-800 border border-yellow-200 @endif">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="font-medium flex items-center gap-1">
                                        @if($req->status === 'pending')
                                            <svg class="w-4 h-4 text-yellow-600 motion-safe:animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"/></svg>
                                        @elseif($req->status === 'approved')
                                            <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        @endif
                                        Request {{ $req->status }}
                                    </span>
                                    <span class="ml-2 block truncate">for
                                        @if($req->subject) {{ $req->subject->name }} @endif
                                        by {{ $req->teacher?->name }}
                                    </span>
                                    @if($req->status === 'rejected' && $req->reason)
                                        <div class="text-xs mt-1">Reason: {{ $req->reason }}</div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 whitespace-nowrap">{{ $req->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    

    @if(true)
        <!-- Browse Classrooms and Request Access -->
        <div class="mt-8 bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Browse Classrooms</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Grade Level</label>
                    <select wire:model="filterGradeLevel" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All</option>
                        @foreach($this->gradeLevels as $level)
                            <option value="{{ $level }}">Grade {{ $level }}</option>
                        @endforeach
                    </select>
            </div>

                <!-- Classrooms -->
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>
                        Classrooms
                    </h3>
                    @if($this->classrooms->isEmpty())
                        <p class="text-sm text-gray-500">No classrooms found.</p>
                    @else
                        @php
                            $uniqueClassrooms = $this->classrooms->unique(function($room){
                                return $room->grade_level . '|' . $room->section;
                            });
                        @endphp
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($uniqueClassrooms as $room)
                                <button wire:click="selectClassroom({{ $room->id }})"
                                    class="w-full text-left p-4 border rounded-lg transition transform hover:scale-[1.01] hover:shadow-sm hover:bg-gray-50 @if($selectedClassroomId === $room->id) border-blue-500 bg-blue-50 @endif">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $room->name }}</div>
                                            <div class="text-sm text-gray-500">Grade {{ $room->grade_level }} • Section {{ $room->section }}</div>
            </div>
                                        @if($selectedClassroomId === $room->id)
                                            <span class="text-blue-600 text-sm">Selected</span>
                                        @endif
        </div>
                        </button>
                            @endforeach
                    </div>
                    @endif
                </div>

                <!-- Teachers for selected classroom -->
                @if($selectedClassroomId)
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5c-2.28-1.32-4.287-3.17-6.16-5.922"/></svg>
                        Teachers
                    </h3>
                    @if($this->teachersForClassroom->isEmpty())
                        <p class="text-sm text-gray-500">No teachers found for this classroom.</p>
                    @else
                        <div class="grid md:grid-cols-3 gap-4">
                            @foreach($this->teachersForClassroom as $teacher)
                                @php
                                    $subjectsInThisClass = $teacher->subjects
                                        ? $teacher->subjects->where('classroom_id', $selectedClassroomId)->pluck('name')->values()
                                        : collect();
                                @endphp
                                <button wire:click="selectTeacher({{ $teacher->id }})"
                                    class="w-full text-left p-4 border rounded-lg transition hover:bg-gray-50 hover:shadow-sm @if($selectedTeacherId === $teacher->id) border-blue-500 bg-blue-50 @endif">
                                    <div class="flex items-start gap-3">
                                        <img src="{{ $teacher->profile_photo_url }}" alt="{{ $teacher->name }}" class="h-10 w-10 rounded-full object-cover">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-gray-900">{{ $teacher->name }}</div>
                                            <div class="text-xs text-gray-500 mb-1">Teacher</div>
                                            @if($subjectsInThisClass->isNotEmpty())
                                                <div class="text-xs text-gray-700 truncate">Subjects: {{ $subjectsInThisClass->join(', ') }}</div>
                                            @else
                                                <div class="text-xs text-gray-400">No subjects listed for this classroom</div>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endif

                <!-- Subjects for selected teacher (show request all before listing) -->
                @if($selectedClassroomId && $selectedTeacherId)
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-lg font-medium text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                            Subjects
                        </span>
                        <button wire:click="requestAllTeachers" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12H8m4-4v8"/></svg>
                            Request All Teachers
                        </button>
                    </div>
                    @if($this->teacherSubjects->isEmpty())
                        <p class="text-sm text-gray-500">No subjects found for this teacher in this classroom.</p>
                    @else
                        <div class="grid md:grid-cols-3 gap-4">
                            @foreach($this->teacherSubjects as $subject)
                                <button wire:click="selectSubject({{ $subject->id }})"
                                    class="w-full text-left p-4 border rounded-lg transition hover:bg-gray-50 hover:shadow-sm @if($selectedSubjectId === $subject->id) border-blue-500 bg-blue-50 @endif">
                                    <div class="font-semibold text-gray-900">{{ $subject->name }}</div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endif

                <!-- Students for selected subject/classroom -->
                @if($selectedClassroomId && $selectedSubjectId)
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-800">Students</h3>
                        <div class="text-xs text-gray-500">Add students to My Students for quick access</div>
                    </div>
                    @php $studentsList = $this->studentsForSelectedSubject; @endphp
                    @if($studentsList->isEmpty())
                        <p class="text-sm text-gray-500">No students found.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($studentsList as $st)
                                <div class="flex items-center justify-between p-3 border rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $st->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $st->email }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if(in_array($st->id, $studentShortcuts))
                                            <button wire:click="removeStudentShortcut({{ $st->id }})" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                                        @else
                                            <button wire:click="addStudentShortcut({{ $st->id }})" class="text-blue-600 hover:text-blue-800 text-sm">Add to My Students</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- My Students Shortcuts -->
        <div class="mt-8 bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">My Students</h2>
            </div>
            <div class="p-6">
                @php $shortcutModels = \App\Models\Student::whereIn('id', $studentShortcuts)->get(); @endphp
                @if($shortcutModels->isEmpty())
                    <p class="text-sm text-gray-500">No students added yet.</p>
                @else
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($shortcutModels as $st)
                            <button wire:click="$set('selectedStudent', {{ $st->id }})" class="text-left p-4 border rounded-lg hover:bg-gray-50">
                                <div class="font-semibold text-gray-900">{{ $st->name }}</div>
                                <div class="text-xs text-gray-500">Grade {{ $st->grade_level }} • Section {{ $st->section }}</div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Request Student Access Modal (opens from Add Student) -->
        <div x-data="{ open: @entangle('showingRequestModal') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Student Access</h3>
                <p class="text-sm text-gray-600 mb-4">Ask the selected teacher to allow you to view student data for this subject.</p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason (optional)</label>
                <textarea wire:model.defer="requestReason" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" rows="3" placeholder="Enter a reason (optional)"></textarea>
                <div class="mt-4 flex justify-end gap-3">
                    <button @click="open=false" class="px-4 py-2 rounded-md border">Cancel</button>
                    <button wire:click="submitAccessRequest({{ $selectedSubjectId ?: 'null' }})" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Send Request</button>
                </div>
            </div>
        </div>

        
    @else
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <p class="text-gray-500">Please select a student to view their information.</p>
        </div>
    @endif
</div>