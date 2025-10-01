<!-- Subject Modal -->
<div x-data="{ show: @entangle('showingSubjectModal').live }"
     x-show="show"
     x-cloak
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-[80] overflow-y-auto"
     x-transition>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-transparent" aria-hidden="true"></div>

        <div x-show="show" x-transition 
             class="relative z-10 inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:max-w-lg sm:w-full">
            <form wire:submit.prevent="saveSubject">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $selectedSubject ? 'Edit Subject' : 'Add New Subject' }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subject Name</label>
                            @if($subjectData['classroom_id'])
                                <div class="mt-1 space-y-2">
                                    @php
                                        $classroom = \App\Models\Classroom::find($subjectData['classroom_id']);
                                        $existingSubjectNames = collect();
                                        if ($classroom) {
                                            $existingSubjectNames = \App\Models\Subject::where('is_active', true)
                                                ->whereHas('classroom', function($q) use ($classroom) {
                                                    $q->where('grade_level', $classroom->grade_level)
                                                      ->where('section', $classroom->section)
                                                      ->where('is_active', true);
                                                })
                                                ->orderBy('name')
                                                ->pluck('name')
                                                ->unique()
                                                ->values();
                                        }
                                    @endphp
                                    @if($existingSubjectNames->count() > 0)
                                        <div class="text-xs text-gray-500">Existing subjects in this classroom (view only):</div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($existingSubjectNames as $sname)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 cursor-default">{{ $sname }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <input type="text" 
                                           wire:model="subjectData.name"
                                           placeholder="Enter new subject name"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
                                </div>
                            @else
                                <input type="text" 
                                       wire:model="subjectData.name"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
                            @endif
                            @error('subjectData.name') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="subjectData.description" 
                                      rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2"></textarea>
                        </div>

                        <!-- Classroom Selection (required before adding subject) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Classroom</label>
                            <select wire:model="subjectData.classroom_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-100 text-gray-700 cursor-not-allowed"
                                    disabled aria-disabled="true">
                                <option value="">Select Classroom</option>
                                @foreach($this->teacherClassrooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->display_name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Classroom selection is fixed for subjects. To change it, close this form and choose a different classroom first.</p>
                            @error('subjectData.classroom_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Show derived Grade Level and Section (read-only) -->
                        @if($subjectData['classroom_id'])
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Grade Level</label>
                                <div class="mt-1 px-3 py-2 rounded-md border border-gray-200 bg-gray-50 text-gray-700">
                                    {{ $selectedGradeLevel ?: (optional(\App\Models\Classroom::find($subjectData['classroom_id']))->grade_level) }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Section</label>
                                <div class="mt-1 px-3 py-2 rounded-md border border-gray-200 bg-gray-50 text-gray-700">
                                    {{ $selectedSection ?: (optional(\App\Models\Classroom::find($subjectData['classroom_id']))->section) }}
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Removed classroom auto-creation UI for subjects; classroom must be pre-created. -->

                        <!-- Hidden field no longer needed as classroom is selected above -->
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        @if($selectedSubject)
                            Update Subject
                        @else
                            Create Subject
                        @endif
                    </button>
                    <button type="button"
                            wire:click="$set('showingSubjectModal', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
