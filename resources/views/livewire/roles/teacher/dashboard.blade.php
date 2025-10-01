{{-- this is the teacher navigation header --}}

<div class="bg-gray-100 min-h-screen">
    <!-- Navigation Tabs -->
    <div class="bg-white shadow-sm border-b sticky top-16 z-40 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mobile Dropdown Navigation -->
            <div class="md:hidden py-2 relative -mx-4 px-4">
                <select
                    class="block max-w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white relative z-50 text-sm"
                    wire:change="setView($event.target.value)"
                    aria-label="Select navigation tab"
                >
                    <option value="dashboard" @if($currentView === 'dashboard') selected @endif>Dashboard</option>
                    <option value="classrooms" @if($currentView === 'classrooms') selected @endif>Classrooms</option>
                    <option value="subjects" @if($currentView === 'subjects') selected @endif>Subjects</option>
                    <option value="attendance" @if($currentView === 'attendance') selected @endif>Attendance</option>
                    <option value="reports" @if($currentView === 'reports') selected @endif>Reports</option>
                    
                    
                </select>
            </div>
            <!-- Desktop Tabs Navigation -->
            <nav class="hidden md:flex space-x-6 overflow-x-auto no-scrollbar" aria-label="Tabs">
                <button wire:click="setView('dashboard')" 
                        class="@if($currentView === 'dashboard') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Dashboard
                </button>
                <button wire:click="setView('classrooms')" 
                        class="@if($currentView === 'classrooms') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Classrooms
                </button>
                <button wire:click="setView('subjects')" 
                        class="@if($currentView === 'subjects') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Subjects
                </button>
                <button wire:click="setView('attendance')" 
                        class="@if($currentView === 'attendance') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Attendance
                </button>
                <button wire:click="setView('reports')" 
                        class="@if($currentView === 'reports') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Reports
                </button>
                
                
            </nav>
        </div>
    </div>

    <!-- Dashboard View -->
    @if($currentView === 'dashboard')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Classrooms</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->dashboardStats['total_classrooms'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->dashboardStats['total_students'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Subjects</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->dashboardStats['total_subjects'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Today's Attendance</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->dashboardStats['today_attendance'] }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button wire:click="setView('classrooms')" 
                        class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors">
                    <div class="text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-gray-900">Manage Classrooms</p>
                    </div>
                </button>

                <button wire:click="setView('students')" 
                        class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors">
                    <div class="text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-gray-900">Manage Students</p>
                    </div>
                </button>

                <button wire:click="setView('attendance')" 
                        class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-colors">
                    <div class="text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-gray-900">Mark Attendance</p>
                    </div>
                </button>

                <button wire:click="setView('reports')" 
                        class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors">
                    <div class="text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-gray-900">View Reports</p>
                    </div>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Include all view sections -->
    @include('livewire.roles.teacher.components.classrooms')
    @include('livewire.roles.teacher.components.students')
    @include('livewire.roles.teacher.components.subjects')
    @include('livewire.roles.teacher.components.attendance')
    @include('livewire.roles.teacher.components.reports')
    
    

    <!-- Modals -->
    @include('livewire.modals.classroom-modal')
    @include('livewire.modals.student-modal')
    @include('livewire.modals.subject-modal')
    @include('livewire.modals.grade-modal')
    @include('livewire.modals.attendance-modal')
    @include('livewire.modals.report-card-modal')
    @include('livewire.modals.report-scope-modal')
    @include('livewire.modals.delete-student-modal')
    @include('livewire.modals.delete-subject-modal')
    
    
    

    <!-- Flash Messages -->
    @if (session()->has('message'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-transition
         x-init="setTimeout(() => show = false, 3000)"
         class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('message') }}
    </div>
    @endif
</div>
