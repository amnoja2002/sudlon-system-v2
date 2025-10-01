<!-- Classrooms View this for classroom to available classroom and configuration -->
@if($currentView === 'classrooms')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800">Classrooms</h2>
                <div class="flex flex-wrap items-center gap-2">
                    <select wire:model.live="filterGradeLevel" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Grades</option>
                        <option value="1">Grade 1</option>
                        <option value="2">Grade 2</option>
                        <option value="3">Grade 3</option>
                        <option value="4">Grade 4</option>
                        <option value="5">Grade 5</option>
                        <option value="6">Grade 6</option>
                    </select>
                    <button type="button" wire:click="showClassroomModal()" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Add Classroom
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade & Section</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Students</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($this->classrooms as $classroom)
                    <tr>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                Grade {{ $classroom->grade_level }} - {{ $classroom->section }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $classroom->students->where('is_active', true)->count() }} / {{ $classroom->max_students }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $classroom->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $classroom->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button type="button" wire:click="selectClassroom({{ $classroom->id }})" 
                                    class="text-blue-600 hover:text-blue-900 mr-3">Manage Students</button>
                            <button type="button" wire:click="selectClassroomForSubjects({{ $classroom->id }})" 
                                    class="text-purple-600 hover:text-purple-900 mr-3">Manage Subjects</button>
                            <button type="button" wire:click="addSubjectForClassroom({{ $classroom->id }})"
                                    class="text-purple-700 hover:text-purple-900 mr-3">Add Subject</button>
                            <button wire:click="exportClassroomStudents({{ $classroom->id }})" 
                                    class="text-green-600 hover:text-green-900 mr-3">Export Students</button>
                            <button wire:click="showClassroomModal({{ $classroom->id }})" 
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button wire:click="$set('confirmingDeleteClassroomId', {{ $classroom->id }})" 
                                    class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            {{ $this->classrooms->links() }}
        </div>
    </div>
</div>
@endif

@if(isset($confirmingDeleteClassroomId) && $confirmingDeleteClassroomId)
<!-- Delete Confirmation Modal -->
<div x-data="{ show: @entangle('confirmingDeleteClassroomId').live }" x-cloak x-show="show" class="fixed inset-0 z-[80]">
    <div class="absolute inset-0 bg-black/50" @click="$wire.set('confirmingDeleteClassroomId', null)"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg font-medium text-gray-900">Confirm Deactivation</h3>
                <p class="mt-2 text-sm text-gray-600">Are you sure you want to deactivate this classroom? This will also deactivate its students, subjects, grades, attendance, and report cards.</p>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="deleteClassroom({{ $confirmingDeleteClassroomId }})" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Deactivate Classroom
                </button>
                <button wire:click="$set('confirmingDeleteClassroomId', null)" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endif
