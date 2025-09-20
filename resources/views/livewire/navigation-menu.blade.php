<header class="sticky top-0 z-50 bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between h-20">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-4" aria-label="DepEd Sudlon II Elementary School - Home">
                <div class="flex items-center gap-3">
                    <!-- DepEd Logo -->
                    <img src="{{ asset('images/logo.jpg') }}" alt="DepEd Logo" class="h-14 w-auto">
                    <div class="h-10 w-px bg-gray-300"></div>
                </div>
                <div>
                    <p class="font-display font-bold text-deped-600 leading-tight">Sudlon Elementary School</p>
                    <p class="text-xs text-gray-600">Department of Education • Region VII</p>
                </div>
            </a>

            <!-- Nav -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-gray-800 hover:text-deped-600 font-medium transition-colors">Home</a>
                <a href="{{ route('about') }}" class="text-gray-800 hover:text-deped-600 font-medium transition-colors">About</a>
                <a href="{{ route('news') }}" class="text-gray-800 hover:text-deped-600 font-medium transition-colors">News</a>
                <a href="{{ route('contact') }}" class="text-gray-800 hover:text-deped-600 font-medium transition-colors">Contact</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <!-- Login/Profile Section -->
                @if(auth()->check())
                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center gap-2 text-gray-700 hover:text-deped-600 focus:outline-none"
                                aria-expanded="false">
                            <span class="hidden sm:inline-block font-medium">{{ Auth::user()->name }}</span>
                            <img src="{{ auth()->user()->profile_photo_url() }}" 
                                 alt="{{ Auth::user()->name }}" 
                                 class="h-8 w-8 rounded-full object-cover">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1"
                             role="menu">

                            <!-- Role-specific Dashboard Links -->
                            @if(auth()->user()->role?->slug === 'principal')
                                <a href="{{ route('principal.dashboard') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-deped-50">
                                    Principal Dashboard
                                </a>
                            @elseif(auth()->user()->role?->slug === 'teacher')
                                <a href="{{ route('teacher.dashboard') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-deped-50">
                                    Teacher Dashboard
                                </a>
                            @elseif(auth()->user()->role?->slug === 'parent')
                                <a href="{{ route('parent.dashboard') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-deped-50">
                                    Parent Dashboard
                                </a>
                            @endif

                            <!-- Common Profile Links -->
                            <a href="{{ route('settings.profile') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-deped-50">
                                Profile Settings
                            </a>
                            <a href="{{ route('settings.password') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-deped-50">
                                Change Password
                            </a>
                            
                            <!-- Logout -->
                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Login Button for Guests -->
                    <div class="flex items-center">
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center gap-2 bg-deped-600 hover:bg-deped-700 text-black px-4 py-2 font-semibold rounded-lg transition-all duration-150 hover:scale-105 active:scale-95">
                            <span class="hidden sm:inline">Portal Login</span>
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </a>
                    </div>
                @endif

                <!-- Mobile Menu Toggle (moved here to right-align it) -->
                <button wire:click="toggleMobileMenu" class="md:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-700" aria-label="Open menu">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Content -->
        <div class="md:hidden absolute inset-x-0 top-full bg-white border-gray-100 shadow-lg transition-all duration-200 ease-in-out {{ $isMobileMenuOpen ? 'opacity-100 visible' : 'opacity-0 invisible' }}">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Home</a>
                <a href="{{ route('about') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">About</a>
                <a href="{{ route('news') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">News</a>
                <a href="{{ route('contact') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Contact</a>

                @if(!auth()->check())
                    <div class="mt-4 pt-4 border-t border-black">
                        <a href="{{ route('login') }}" 
                           class="flex items-center justify-center gap-2 bg-deped-600 hover:bg-deped-700 text-black px-4 py-2 font-semibold rounded-lg transition-all duration-150">
                            Portal Login
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>
