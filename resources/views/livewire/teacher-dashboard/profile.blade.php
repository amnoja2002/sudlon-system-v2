<!-- Profile View -->
@if($currentView === 'profile')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Profile Settings</h2>
                <button wire:click="showProfileModal()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Edit Profile
                </button>
            </div>
        </div>

        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Role</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->role->name ?? 'Teacher' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Days since last update</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @php($days = now()->startOfDay()->diffInDays(optional(auth()->user()->updated_at)->startOfDay()))
                                {{ $days === 0 ? 'Updated today' : $days . ' day' . ($days > 1 ? 's' : '') . ' ago' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Teaching Statistics</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Classrooms</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $this->dashboardStats['total_classrooms'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Students</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $this->dashboardStats['total_students'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Subjects</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $this->dashboardStats['total_subjects'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
