<!-- Student Modal -->
<div x-data="{ show: @entangle('showingStudentModal').live }"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-[70] overflow-y-auto"
     role="dialog" aria-modal="true" x-transition>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60" aria-hidden="true"></div>

        <div x-show="show" x-transition 
             class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form wire:submit.prevent="saveStudent">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $selectedStudent ? 'Edit Student' : 'Add New Student' }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" 
                                   wire:model="studentData.name"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
                            @error('studentData.name') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" 
                                   wire:model="studentData.email"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
                            @error('studentData.email') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Grade Level</label>
                                <input type="text" value="{{ $studentData['grade_level'] ?? '' }}" disabled
                                       class="mt-1 block w-full rounded-md border-gray-200 bg-gray-100 text-gray-700 shadow-sm px-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Section</label>
                                <input type="text" value="{{ $studentData['section'] ?? '' }}" disabled
                                       class="mt-1 block w-full rounded-md border-gray-200 bg-gray-100 text-gray-700 shadow-sm px-2">
                            </div>
                        </div>

                        @if($selectedStudent)
                            @php $availableClassrooms = $this->availableClassrooms; @endphp
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Classroom</label>
                                <select wire:model="studentData.classroom_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2"
                                        @if(($availableClassrooms->count() ?? 0) <= 1) disabled @endif>
                                    @foreach($availableClassrooms as $classroom)
                                        <option value="{{ $classroom->id }}">{{ $classroom->name }} - Grade {{ $classroom->grade_level }} {{ $classroom->section }}</option>
                                    @endforeach
                                </select>
                                @if(($availableClassrooms->count() ?? 0) <= 1)
                                    <p class="mt-1 text-xs text-gray-500">No other classroom available to transfer.</p>
                                @else
                                    <p class="mt-1 text-xs text-gray-500">Select a classroom to transfer this student. Grade/Section will update automatically.</p>
                                @endif
                                @error('studentData.classroom_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            @if($selectedClassroom)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Classroom</label>
                                    <div class="mt-1 px-3 py-2 rounded-md bg-blue-50 text-blue-800 text-sm">
                                        Assigned to current classroom
                                    </div>
                                </div>
                            @else
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Classroom</label>
                                    <select wire:model="studentData.classroom_id" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
                                        <option value="">Select Classroom</option>
                                        @foreach($this->classrooms as $classroom)
                                            <option value="{{ $classroom->id }}">{{ $classroom->name }} - Grade {{ $classroom->grade_level }} {{ $classroom->section }}</option>
                                        @endforeach
                                    </select>
                                    @error('studentData.classroom_id') 
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    wire:click="$set('showingStudentModal', false)"
                                    class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ $selectedStudent ? 'Update Student' : 'Add Student' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
