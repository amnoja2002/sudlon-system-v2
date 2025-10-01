<!-- Classroom Modal -->
<div x-data="{ show: @entangle('showingClassroomModal').live }"
     x-show="show"
     x-cloak
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-[70] overflow-y-auto"
     role="dialog" aria-modal="true" x-transition>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-transparent" aria-hidden="true"></div>

        <div x-show="show" x-transition 
             class="relative z-10 inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:max-w-lg sm:w-full">
            <form wire:submit.prevent="saveClassroom">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $selectedClassroom ? 'Edit Classroom' : 'Add New Classroom' }}
                    </h3>
                    
                    <div class="space-y-4">
                        

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Grade Level</label>
                                <select wire:model.live="classroomData.grade_level" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Grade</option>
                                    <option value="1">Grade 1</option>
                                    <option value="2">Grade 2</option>
                                    <option value="3">Grade 3</option>
                                    <option value="4">Grade 4</option>
                                    <option value="5">Grade 5</option>
                                    <option value="6">Grade 6</option>
                                </select>
                                @error('classroomData.grade_level') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Section</label>
                                @if($classroomData['grade_level'])
                                    <div class="mt-1 space-y-2">
                                        <select wire:model.live="classroomData.section" 
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Section</option>
                                            @foreach($availableClassroomSections as $section)
                                                <option value="{{ $section }}">{{ $section }}</option>
                                            @endforeach
                                            <option value="new">Create New Section</option>
                                        </select>
                                        
                                        @if($classroomData['section'] === 'new')
                                        <input type="text" 
                                               wire:model.live="newSectionName"
                                               placeholder="Enter new section name"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
                                        @endif
                                    </div>
                                @else
                                    <input type="text" 
                                           disabled
                                           placeholder="Select grade first"
                                           class="mt-1 block w-full rounded-md border-gray-200 bg-gray-100 text-gray-500 px-2">
                                @endif
                                @error('classroomData.section') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>



                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="classroomData.description" 
                                      rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Max Students</label>
                            <input type="number" 
                                   wire:model="classroomData.max_students"
                                   min="1" max="50"
                                   @if($lockMaxStudents) disabled aria-disabled="true" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2 bg-gray-100 text-gray-700 cursor-not-allowed" @else class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2" @endif>
                            @error('classroomData.max_students') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ $selectedClassroom ? 'Update Classroom' : 'Add Classroom' }}
                    </button>
                    <button type="button"
                            wire:click="$set('showingClassroomModal', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
