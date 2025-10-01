<!-- Grade Modal this modal is for principal configuration  -->

<div x-data="{ show: @entangle('showingGradeModal') }"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="show" class="fixed inset-0 bg-transparent"></div>

        <div x-show="show" class="relative z-10 inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form wire:submit.prevent="saveGrade">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Add/Edit Grade
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Student</label>
                            <select wire:model="gradeData.student_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Student</option>
                                @foreach($this->students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} - Grade {{ $student->grade_level }} {{ $student->section }}</option>
                                @endforeach
                            </select>
                            @error('gradeData.student_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" 
                                   wire:model="gradeData.subject"
                                   placeholder="e.g., Mathematics, Science, English"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('gradeData.subject') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Term</label>
                            <select wire:model="gradeData.term" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Term</option>
                                <option value="1st Quarter">1st Quarter</option>
                                <option value="2nd Quarter">2nd Quarter</option>
                                <option value="3rd Quarter">3rd Quarter</option>
                                <option value="4th Quarter">4th Quarter</option>
                            </select>
                            @error('gradeData.term') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Score</label>
                            <input type="text" 
                                   wire:model.lazy="gradeData.score"
                                   x-inputmode="decimal"
                                   pattern="^\\d+\\.\\d{2}$"
                                   placeholder="60.00 - 99.99"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Format: two decimals (e.g., 85.50), range 60.00–99.99</p>
                            @error('gradeData.score') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save Grade
                    </button>
                    <button type="button"
                            wire:click="$set('showingGradeModal', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
