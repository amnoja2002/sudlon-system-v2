<!-- Subject Modal -->
<div x-data="{ show: @entangle('showingSubjectModal').live }"
     x-show="show"
     x-cloak
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-[80] overflow-y-auto"
     x-transition>
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/60" aria-hidden="true"></div>

        <div x-show="show" x-transition 
             class="relative z-10 inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form wire:submit.prevent="saveSubject">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $selectedSubject ? 'Edit Subject' : 'Add New Subject' }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subject Name</label>
                            <input type="text" 
                                   wire:model="subjectData.name"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Classroom</label>
                            @php
                                $currentClassroom = $this->classrooms->firstWhere('id', $subjectData['classroom_id']);
                            @endphp
                            <input type="text"
                                   value="{{ $currentClassroom ? ($currentClassroom->name . ' - Grade ' . $currentClassroom->grade_level . ' ' . $currentClassroom->section) : '' }}"
                                   disabled
                                   class="mt-1 block w-full rounded-md border-gray-200 bg-gray-100 text-gray-700 shadow-sm px-2">
                            <input type="hidden" wire:model="subjectData.classroom_id">
                            @error('subjectData.classroom_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Grade Level</label>
                                <input type="text" wire:model.live="subjectData.grade_level" placeholder="e.g., 1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Section</label>
                                <input type="text" wire:model.live="subjectData.section" placeholder="e.g., Mabini" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
                            </div>
                        </div>

                        @php $suggest = $this->suggestedClassroomForSubject; @endphp
                        @if($suggest)
                            <div class="p-3 rounded-md border border-blue-200 bg-blue-50 text-blue-800 text-sm flex items-center justify-between">
                                <div>
                                    Found existing classroom: <span class="font-medium">Grade {{ $suggest->grade_level }} - {{ $suggest->section }}</span> ({{ $suggest->name }}) with {{ $suggest->students()->count() }} students.
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="useSuggestedClassroomForSubject({{ $suggest->id }})" class="px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700">Use</button>
                                    <a href="#" wire:click.prevent="$set('selectedClassroomForSubjects', {{ $suggest->id }})" class="text-blue-700 underline text-xs">View Students</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ $selectedSubject ? 'Update Subject' : 'Add Subject' }}
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
