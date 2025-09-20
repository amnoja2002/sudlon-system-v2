<!-- Students View -->
@if($currentView === 'students')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800">Students</h2>
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="showStudentModal()" 
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Add Student
                    </button>
                    <button type="button" wire:click="backToClassrooms"
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Back to Classrooms
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade & Section</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classroom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($this->students as $student)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $student->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                Grade {{ $student->grade_level }} - {{ $student->section }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $student->classroom ? $student->classroom->name : 'No Classroom' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            
                            <button wire:click="showStudentModal({{ $student->id }})" 
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button wire:click="deleteStudent({{ $student->id }})" 
                                    class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            {{ $this->students->links() }}
        </div>
    </div>
</div>
@endif
