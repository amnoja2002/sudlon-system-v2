<div class="bg-gray-100 min-h-screen dark:bg-gray-100 dark:text-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start max-md:flex-col">
            <!-- Settings Navigation -->
            <div class="me-10 w-full pb-4 md:w-[280px]">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 dark:bg-white dark:text-black">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Settings</h2>
                    <nav class="space-y-2">
                        <a href="{{ route('settings.profile') }}" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('settings.profile') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile
                        </a>
                        <a href="{{ route('settings.password') }}" 
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('settings.password') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Password & Security
                        </a>
                        
                    </nav>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="flex-1 self-stretch max-md:pt-6 dark:bg-white dark:text-black">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
