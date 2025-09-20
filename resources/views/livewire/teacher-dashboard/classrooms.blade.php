<!-- Classrooms View -->
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
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
                            <div class="text-sm font-medium text-gray-900">{{ $classroom->name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($classroom->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                Grade {{ $classroom->grade_level }} - {{ $classroom->section }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $classroom->students->count() }} / {{ $classroom->max_students }}
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
                            <button wire:click="deleteClassroom({{ $classroom->id }})" 
                                    class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Are you sure?')">Delete</button>
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
