<div>
    <div class="space-y-16">
        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-6 pt-6 md:pt-12">
            <div class="grid md:grid-cols-2 gap-10 items-center">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="bg-red text-gray-900 px-3 py-1 rounded-full text-sm font-semibold">Official DepEd School</span>
                        <span class="bg-gold-300 text-gray-900 px-3 py-1 rounded-full text-sm font-semibold">Region VII</span>
                    </div>
                    <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 mt-5">
                        Nurturing Future Leaders Through
                        <span class="text-black">Excellence in Education</span>
                    </h1>
                    <p class="text-lg text-gray-700 mt-4 max-w-xl">
                        Welcome to Sudlon II Elementary School—committed to providing quality, accessible, and values-based education in accordance with DepEd standards.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('about') }}" class="bg-deped-600 text-black px-5 py-3 font-semibold hover:bg-deped-700 rounded-lg transition">Learn More</a>
                        <a href="#enrollment" class="bg-red text-black px-5 py-3 font-semibold hover:bg-red/90 rounded-lg transition">Enroll Now</a>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-8 grid grid-cols-3 gap-4 max-w-md">
                        <div class="rounded-lg bg-white shadow-md p-4 text-center border-t-4 border-deped-600">
                            <p class="text-2xl font-bold text-deped-600">500+</p>
                            <p class="text-sm text-gray-700 font-medium">Students</p>
                        </div>
                        <div class="rounded-lg bg-white shadow-md p-4 text-center border-t-4 border-gold-400">
                            <p class="text-2xl font-bold text-gold-600">30</p>
                            <p class="text-sm text-gray-700 font-medium">Teachers</p>
                        </div>
                        <div class="rounded-lg bg-white shadow-md p-4 text-center border-t-4 border-red">
                            <p class="text-2xl font-bold text-red">K-6</p>
                            <p class="text-sm text-gray-700 font-medium">Levels</p>
                        </div>
                    </div>
                </div>

                <!-- Image and Vision -->
                <div class="relative">
                    <div class="absolute -top-6 -left-6 w-24 h-24 bg-deped-200 blur-2xl rounded-full"></div>
                    <div class="absolute -bottom-8 -right-6 w-24 h-24 bg-gold-200 blur-2xl rounded-full"></div>
                    <div class="rounded-2xl bg-white shadow-md p-6 relative z-10">
                        <div class="aspect-video rounded-lg overflow-hidden mb-4">
                            <img src="{{ asset('sudlon-system/img/tst1.jpg') }}" alt="Sudlon II Elementary School Building" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-12 bg-deped-600"></div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Our Vision</h3>
                                    <p class="text-sm text-gray-600">We envision well-educated and competitive lifelong learners who actively participate in community development.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-12 bg-red"></div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Core Values</h3>
                                    <p class="text-sm text-gray-600">Maka-Diyos, Maka-tao, Makakalikasan, at Makabansa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="max-w-7xl mx-auto px-6 mt-12">
            <div class="grid md:grid-cols-4 gap-6">
                @foreach($quickLinks as $link)
                <a href="{{ $link['link'] }}" class="group">
                    <div class="h-full rounded-xl bg-white shadow-md p-6 hover:shadow-lg transition">
                        <div class="w-12 h-12 rounded-lg bg-deped-50 text-deped-600 flex items-center justify-center mb-4 group-hover:bg-deped-100 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                {{-- Icon paths handled by Blade --}}
                                @if($link['icon'] === 'clipboard-list')
                                    <!-- Heroicons: clipboard-document-list -->
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m.75-12a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0115.75 20.25H8.25A2.25 2.25 0 016 18V6.75A2.25 2.25 0 018.25 4.5h1.982a2.25 2.25 0 004.536 0H16.5z" />
                                @elseif($link['icon'] === 'calendar')
                                    <!-- Heroicons: calendar-days -->
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 8.25h18M4.5 6.75h15a1.5 1.5 0 011.5 1.5V18a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V8.25a1.5 1.5 0 011.5-1.5zM8.25 12h.008v.008H8.25V12zM11.25 12h.008v.008h-.008V12zM14.25 12h.008v.008h-.008V12zM8.25 15h.008v.008H8.25V15zM11.25 15h.008v.008h-.008V15zM14.25 15h.008v.008h-.008V15z" />
                                @elseif($link['icon'] === 'academic-cap')
                                    <!-- Heroicons: academic-cap -->
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25L3.75 9 12 3.75 20.25 9 12 14.25z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 10.5v4.5a.75.75 0 00.42.67l4.5 2.25a.75.75 0 00.66 0l4.5-2.25a.75.75 0 00.42-.67v-4.5" />
                                @else
                                    <!-- Default: information-circle -->
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25v3.75m0-7.5h.008v.008h-.008V7.5zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                @endif
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-deped-600 transition">{{ $link['title'] }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $link['description'] }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 mt-16 mb-24">
            <livewire:contact-form />
        </div>
    </div>
</div>
