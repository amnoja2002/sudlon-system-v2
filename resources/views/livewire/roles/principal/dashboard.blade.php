{{-- this is the principal dashboard --}}

<div class="bg-gray-100 min-h-screen">
    <!-- Top Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
        <!-- Total Students -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 mr-4">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Total Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->studentCount }}</p>
                </div>
            </div>
        </div>

        <!-- Total Teachers -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-deped-100 mr-4">
                    <svg class="h-8 w-8 text-deped-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Total Teachers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->teacherCount }}</p>
                </div>
            </div>
        </div>

        <!-- Total Parents -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-deped-100 mr-4">
                    <svg class="h-8 w-8 text-deped-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Total Parents</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->parentCount }}</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Content -->
    <div class="p-4">
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xl font-semibold text-gray-800">Manage Users</h2>
                    <div class="flex items-center gap-2">
                        <button class="px-3 py-2 text-sm rounded-md border @if(!$filterRoleSlug) bg-gray-100 @endif" wire:click="showAllUsers">All</button>
                        <button class="px-3 py-2 text-sm rounded-md border @if($filterRoleSlug==='teacher') bg-gray-100 @endif" wire:click="showTeachers">Teachers</button>
                        <button class="px-3 py-2 text-sm rounded-md border @if($filterRoleSlug==='parent') bg-gray-100 @endif" wire:click="showParents">Parents</button>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                                wire:click="showUserModal">
                            Add New User
                        </button>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($this->users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($user->role?->slug === 'teacher') bg-green-50 text-green-700
                                    @elseif($user->role?->slug === 'student') bg-blue-50 text-blue-700
                                    @elseif($user->role?->slug === 'parent') bg-purple-50 text-purple-700
                                    @else bg-gray-50 text-gray-700
                                    @endif">
                                    {{ $user->role?->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button" class="text-blue-600 hover:text-blue-900 mr-3"
                                        wire:click="showUserModal({{ $user->id }})">
                                    Edit
                                </button>
                                <button type="button" class="text-red-600 hover:text-red-900"
                                        wire:click="confirmDeactivate({{ $user->id }})">
                                    Deactivate
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4">
                {{ $this->users->links() }}
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div x-data="{ show: @entangle('showingUserModal').live }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-[70] overflow-y-auto"
         x-transition>
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <div x-show="show" class="fixed inset-0 bg-black/50 transition-opacity"></div>

            <div x-show="show" class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $selectedUser ? 'Edit User' : 'Add New User' }}
                    </h3>
                    
                    <div class="mt-4">
                        @if($selectedUser)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" wire:model="userData.name" class="mt-1 block w-full rounded-md border border-gray-300 px-2">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" wire:model="userData.email" class="mt-1 block w-full rounded-md border border-gray-300 px-2">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Role</label>
                                <select wire:model.live="selectedRole" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" wire:model="userData.name" class="mt-1 block w-full rounded-md border border-gray-300 px-2">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" wire:model="userData.email" class="mt-1 block w-full rounded-md border border-gray-300 px-2">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" wire:model="userData.password" class="mt-1 block w-full rounded-md border border-gray-300 px-2">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Role</label>
                                <select wire:model="userData.role_id" class="mt-1 block w-full rounded-md border border-gray-300 px-2">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>

    
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    @if($selectedUser)
                        <button wire:click="updateUserRole" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save Changes
                        </button>
                    @else
                        <button wire:click="createUser" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Create User
                        </button>
                    @endif
                    <button wire:click="$set('showingUserModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Deactivate Confirmation Modal -->
    <div x-data="deactivateModal()" x-cloak x-show="open" class="fixed inset-0 z-[80]">
        <div class="absolute inset-0 bg-black/50" @click="close()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Confirm Deactivation</h3>
                    <p class="mt-2 text-sm text-gray-600">Please wait <span x-text="seconds"></span>s before confirming.</p>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2"
                            :class="ready ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-gray-300 text-gray-600 cursor-not-allowed mx-2'"
                            :disabled="!ready"
                            @click="$wire.deactivateUserConfirmed(); close()">
                        <span x-show="!ready">Confirm in <span x-text="seconds"></span>s</span>
                        <span x-show="ready">Confirm Deactivate</span>
                    </button>
                    <button @click="close()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm px-2">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deactivateModal() {
            return {
                open: @entangle('showingDeactivateModal').live,
                seconds: 10,
                ready: false,
                timer: null,
                start() {
                    this.seconds = 10;
                    this.ready = false;
                    if (this.timer) clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        if (this.seconds > 0) this.seconds--;
                        if (this.seconds === 0) { this.ready = true; clearInterval(this.timer); this.timer = null; }
                    }, 1000);
                },
                close() {
                    if (this.timer) { clearInterval(this.timer); this.timer = null; }
                    this.open = false;
                },
                init() {
                    this.$watch('open', (v) => { if (v) this.start(); else this.close(); });
                }
            }
        }
    </script>
</div>