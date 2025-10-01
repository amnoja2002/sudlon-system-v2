<!-- Attendance View this is the part to interact with attendance for teacher -->

@if($currentView === 'attendance')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(!$selectedClassroomForAttendance)
    <!-- Classroom Selection -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Select Classroom to Manage Attendance</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->classrooms as $classroom)
                <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" 
                     wire:click="selectClassroomForAttendance({{ $classroom->id }})">
                    <h3 class="font-medium text-gray-900">{{ $classroom->name }}</h3>
                    <p class="text-sm text-gray-500">Grade {{ $classroom->grade_level }} - {{ $classroom->section }}</p>
                    <p class="text-sm text-gray-500">{{ $classroom->students->count() }} students</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <!-- Attendance Management for Selected Classroom -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Attendance
                    </h2>
                    <p class="text-sm text-gray-500">
                        Grade {{ $this->classrooms->where('id', $selectedClassroomForAttendance)->first()->grade_level }} - 
                        {{ $this->classrooms->where('id', $selectedClassroomForAttendance)->first()->section }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="exportAttendance({{ $selectedClassroomForAttendance }})" 
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Export Attendance
                    </button>
                    @if($selectedSubject)
                    <button wire:click="showAttendanceLog" 
                            class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                        View Attendance Log
                    </button>
                    @endif
                    <button wire:click="setView('attendance')" 
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Back to Classrooms
                    </button>
                </div>
            </div>
        </div>

        <!-- Subject Selection -->
        @if(!$selectedSubject)
        <div class="px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Select Subject</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @forelse($this->subjectsForAttendance as $subject)
                <div class="border rounded-md p-3 hover:bg-gray-50 cursor-pointer"
                     wire:click="selectSubjectForAttendance({{ $subject->id }})">
                    <div class="font-medium text-gray-900">{{ $subject->name }}</div>
                    <div class="text-sm text-gray-500">{{ $subject->classroom->name }}</div>
                </div>
                @empty
                <div class="text-sm text-gray-500">No subjects found for this classroom.</div>
                @endforelse
            </div>
        </div>
        @endif

        @if($currentAttendanceView === 'form' && $showingAttendanceForm && $selectedSubject)
        <!-- Attendance Form -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Mark Attendance for {{ $attendanceData['date'] ? \Carbon\Carbon::parse($attendanceData['date'])->format('M d, Y') : 'Today' }}</h3>
            
            <!-- Students to Mark -->
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-800 mb-3">Students to Mark</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($this->classrooms->where('id', $selectedClassroomForAttendance)->first()->students as $student)
                        @if(isset($studentAttendanceData[$student->id]) && $studentAttendanceData[$student->id] === 'unmarked')
                        <div class="border rounded-lg p-3 bg-white">
                            <div class="text-sm font-medium text-gray-900 mb-2">{{ $student->name }}</div>
                            <div class="flex flex-wrap gap-2">
                                <button wire:click="updateStudentAttendance({{ $student->id }}, 'present')" 
                                        class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full hover:bg-green-200">
                                    Present
                                </button>
                                <button wire:click="updateStudentAttendance({{ $student->id }}, 'absent')" 
                                        class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded-full hover:bg-red-200">
                                    Absent
                                </button>
                                <button wire:click="updateStudentAttendance({{ $student->id }}, 'late')" 
                                        class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full hover:bg-yellow-200">
                                    Late
                                </button>
                                <button wire:click="updateStudentAttendance({{ $student->id }}, 'excused')" 
                                        class="px-3 py-1 text-xs bg-purple-100 text-purple-800 rounded-full hover:bg-purple-200">
                                    Excused
                                </button>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Marked Students -->
            @if(collect($studentAttendanceData)->whereIn('status', ['present', 'absent', 'late', 'excused'])->count() > 0 || collect($studentAttendanceData)->where('status', 'like', 'recorded_%')->count() > 0)
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-800 mb-3">Marked Students</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($this->classrooms->where('id', $selectedClassroomForAttendance)->first()->students as $student)
                        @if(isset($studentAttendanceData[$student->id]) && (in_array($studentAttendanceData[$student->id], ['present', 'absent', 'late', 'excused']) || str_starts_with($studentAttendanceData[$student->id], 'recorded_')))
                        <div class="border rounded-lg p-3 bg-white">
                            <div class="text-sm font-medium text-gray-900 mb-2">{{ $student->name }}</div>
                            <div class="flex items-center justify-between">
                                @php
                                    $status = $studentAttendanceData[$student->id];
                                    $isRecorded = str_starts_with($status, 'recorded_');
                                    $displayStatus = $isRecorded ? substr($status, 9) : $status; // Remove 'recorded_' prefix
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $displayStatus === 'present' ? 'bg-green-100 text-green-800' : 
                                       ($displayStatus === 'absent' ? 'bg-red-100 text-red-800' : 
                                       ($displayStatus === 'late' ? 'bg-yellow-100 text-yellow-800' : 
                                       'bg-purple-100 text-purple-800')) }}">
                                    {{ ucfirst($displayStatus) }}
                                    @if($isRecorded)
                                        <span class="text-xs text-gray-500 ml-1">(Recorded)</span>
                                    @endif
                                </span>
                                @if($isRecorded)
                                    <button wire:click="deleteExistingAttendance({{ $student->id }})" 
                                            class="text-xs text-red-600 hover:text-red-800">
                                        Delete & Re-mark
                                    </button>
                                @else
                                    <button wire:click="updateStudentAttendance({{ $student->id }}, 'unmarked')" 
                                            class="text-xs text-gray-500 hover:text-red-600">
                                        Undo
                                    </button>
                                @endif
                            </div>
                                </div>
                        @endif
                        @endforeach
                </div>
            </div>
            @endif

            <div class="mt-4 flex justify-end space-x-3">
                <button wire:click="saveAttendance()" 
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    Save Attendance
                </button>
                <button wire:click="cancelAttendance()" 
                        class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Cancel
                </button>
            </div>
        </div>
        @endif

        @if($currentAttendanceView === 'log' && $selectedSubject)
        <!-- Attendance Log View -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Attendance Log</h3>
                    @php $displayLogDate = $attendanceLogDate ? \Carbon\Carbon::parse($attendanceLogDate)->format('M d, Y') : now()->format('M d, Y'); @endphp
                    <p class="text-sm text-gray-600 mt-1">Date: {{ $displayLogDate }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="prevAttendanceLogDay" class="px-3 py-2 rounded-md border hover:bg-gray-50">Prev Day</button>
                    <button wire:click="nextAttendanceLogDay" class="px-3 py-2 rounded-md border hover:bg-gray-50">Next Day</button>
                    <button wire:click="backToAttendanceFormView" class="px-3 py-2 rounded-md border hover:bg-gray-50">Back</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $logDate = $attendanceLogDate ? \Carbon\Carbon::parse($attendanceLogDate)->format('M d, Y') : now()->format('M d, Y'); @endphp
                        @forelse($this->attendanceLog as $att)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $att->student->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $att->status === 'present' ? 'bg-green-100 text-green-800' : ($att->status === 'absent' ? 'bg-red-100 text-red-800' : ($att->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800')) }}">
                                    {{ ucfirst($att->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-sm text-gray-500">No attendance found for @php echo $logDate; @endphp</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Attendance History -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($this->attendance as $attendance)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $attendance->student->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $attendance->date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : ($attendance->status === 'absent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button wire:click="openDeleteAttendanceModal({{ $attendance->id }})" 
                                    class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            {{ $this->attendance->links() }}
        </div>
    </div>
    @endif
</div>
@endif

@if(isset($confirmingDeleteAttendanceId) && $confirmingDeleteAttendanceId)
<!-- Delete Attendance Confirmation Modal -->
<div x-data="{ show: @entangle('confirmingDeleteAttendanceId').live }" x-cloak x-show="show" class="fixed inset-0 z-[80]">
    <div class="absolute inset-0 bg-black/50" @click="$wire.set('confirmingDeleteAttendanceId', null)"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg font-medium text-gray-900">Confirm Deletion</h3>
                <p class="mt-2 text-sm text-gray-600">Are you sure you want to delete this attendance record? This action cannot be undone.</p>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="confirmDeleteAttendance" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Delete Attendance
                </button>
                <button wire:click="cancelDeleteAttendance" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endif
