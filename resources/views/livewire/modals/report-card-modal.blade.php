<!-- Report Card Modal -->
<div x-data="{ show: @entangle('showingReportCardModal') }"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition>
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <form wire:submit.prevent="generateReportCard">
                <div class="bg-white px-6 pt-5 pb-4 sm:p-8 sm:pb-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">
                        Generate DEPED Report Card
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Student</label>
                            <select wire:model="reportCardData.student_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Student</option>
                                @foreach($this->students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} - Grade {{ $student->grade_level }} {{ $student->section }}</option>
                                @endforeach
                            </select>
                            @error('reportCardData.student_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Classroom</label>
                            <select wire:model="reportCardData.classroom_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Classroom</option>
                                @foreach($this->classrooms as $classroom)
                                    <option value="{{ $classroom->id }}">{{ $classroom->name }} - Grade {{ $classroom->grade_level }} {{ $classroom->section }}</option>
                                @endforeach
                            </select>
                            @error('reportCardData.classroom_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">School Year</label>
                            <input type="text" 
                                   wire:model="reportCardData.school_year"
                                   placeholder="e.g., 2024-2025"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('reportCardData.school_year') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quarter / Semester</label>
                            <select wire:model="reportCardData.semester" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Semester</option>
                                <option value="1st Quarter">1st Quarter</option>
                                <option value="2nd Quarter">2nd Quarter</option>
                                <option value="3rd Quarter">3rd Quarter</option>
                                <option value="4th Quarter">4th Quarter</option>
                            </select>
                            @error('reportCardData.semester') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- DEPED-style Report Card Preview -->
                        @if($reportCardData['student_id'] && $reportCardData['classroom_id'])
                        <div class="border-2 border-gray-300 rounded-lg p-6 bg-gradient-to-br from-blue-50 to-white">
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-blue-800 mb-2">REPORT CARD</h3>
                                <p class="text-sm text-gray-600">Republic of the Philippines</p>
                                <p class="text-sm text-gray-600">Department of Education</p>
                                <p class="text-lg font-semibold text-gray-800 mt-2">SUDLON ELEMENTARY SCHOOL</p>
                            </div>
                            
                            @php
                                $student = \App\Models\Student::find($reportCardData['student_id']);
                                $classroom = \App\Models\Classroom::find($reportCardData['classroom_id']);
                                
                                // Get all subjects for this teacher in the same grade level and section
                                $subjects = \App\Models\Subject::where('teacher_id', auth()->id())
                                    ->whereHas('classroom', function($query) use ($classroom) {
                                        $query->where('grade_level', $classroom->grade_level)
                                              ->where('section', $classroom->section)
                                              ->where('is_active', true);
                                    })
                                    ->where('is_active', true)
                                    ->get();
                                    
                                $totalScore = 0;
                                $subjectCount = 0;
                            @endphp
                            
                            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                                <div>
                                    <p><span class="font-semibold">Student Name:</span> {{ $student->name }}</p>
                                    <p><span class="font-semibold">Grade & Section:</span> Grade {{ $classroom->grade_level }} - {{ $classroom->section }}</p>
                                </div>
                                <div>
                                    <p><span class="font-semibold">School Year:</span> {{ $reportCardData['school_year'] ?? '2024-2025' }}</p>
                                    <p><span class="font-semibold">Quarter:</span> {{ $reportCardData['semester'] ?? '1st Quarter' }}</p>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-gray-400">
                                    <thead>
                                        <tr class="bg-blue-100">
                                            <th class="border border-gray-400 px-3 py-2 text-left font-semibold text-gray-800">SUBJECTS</th>
                                            <th class="border border-gray-400 px-3 py-2 text-center font-semibold text-gray-800">GRADE</th>
                                            <th class="border border-gray-400 px-3 py-2 text-center font-semibold text-gray-800">REMARKS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjects as $subject)
                                        @php
                                            $grades = $subject->grades()->where('student_id', $reportCardData['student_id'])->get();
                                            $grade = $grades->count() > 0 ? round($grades->avg('score')) : 0;
                                            $totalScore += $grade;
                                            $subjectCount++;
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-400 px-3 py-2 text-sm font-medium text-gray-900">{{ $subject->name }}</td>
                                            <td class="border border-gray-400 px-3 py-2 text-center text-sm font-bold {{ $grade >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $grade > 0 ? $grade : '-' }}</td>
                                            <td class="border border-gray-400 px-3 py-2 text-center text-sm {{ $grade >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $grade >= 75 ? 'PASSED' : ($grade > 0 ? 'FAILED' : 'NO GRADE') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-100">
                                        @php
                                            $average = $subjectCount > 0 ? round($totalScore / $subjectCount, 2) : 0;
                                        @endphp
                                        <tr>
                                            <td class="border border-gray-400 px-3 py-2 text-sm font-bold text-gray-900">GENERAL AVERAGE</td>
                                            <td class="border border-gray-400 px-3 py-2 text-center text-sm font-bold {{ $average >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $average > 0 ? $average : '-' }}</td>
                                            <td class="border border-gray-400 px-3 py-2 text-center text-sm font-bold {{ $average >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $average >= 75 ? 'PASSED' : ($average > 0 ? 'FAILED' : 'NO GRADE') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <div class="mt-6 text-center">
                                <p class="text-sm text-gray-600 mb-4">This report card is generated on {{ now()->format('F d, Y') }}</p>
                                <div class="flex justify-between items-end">
                                    <div class="text-center">
                                        <p class="text-sm font-semibold text-gray-800">Class Adviser</p>
                                        <p class="text-sm text-gray-600">{{ auth()->user()->name }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-semibold text-gray-800">Principal</p>
                                        <p class="text-sm text-gray-600">School Principal</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teacher Comments</label>
                            <textarea wire:model="reportCardData.teacher_comments" 
                                      rows="3"
                                      placeholder="Optional comments about the student's performance..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Generate Report Card
                    </button>
                    <button type="button"
                            wire:click="$set('showingReportCardModal', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
