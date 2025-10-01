<div class="max-w-7xl mx-auto px-6 pt-10 md:pt-16 pb-20">
    <div class="space-y-16">
        <!-- Hero Section -->
        <div class="grid md:grid-cols-2 gap-10 items-center">
            <div>
                <h1 class="font-display text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">
                    About Our School
                </h1>
                <p class="text-lg text-slate-600 mt-4 max-w-xl">
                    Sudlon City Elementary School is a community-centered public elementary school focused on nurturing young minds through innovative teaching methods and a supportive learning environment.
                </p>
            </div>

            <!-- Image Carousel -->
            <div class="relative"
                x-data="{
                    images: [
                        '/sudlon-system/img/crs2.jpg',
                        '/sudlon-system/img/cs1.jpg',
                        '/sudlon-system/img/t3.jpg',
                    ],
                    currentIndex: 0,
                    next() {
                        this.currentIndex = (this.currentIndex + 1) % this.images.length;
                    },
                    prev() {
                        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                    },
                    init() {
                        setInterval(() => this.next(), 5000); // Auto-slide every 5s
                    }
                }"
                x-init="init()"
            >
                <!-- Gradient Background Blobs -->
                <div class="absolute -top-6 -left-6 w-24 h-24 bg-sun/40 blur-2xl rounded-full"></div>
                <div class="absolute -bottom-8 -right-6 w-24 h-24 bg-berry/20 blur-2xl rounded-full"></div>

                <!-- Carousel Card -->
                <div class="rounded-3xl bg-white/80 border border-white/60 p-6 shadow-soft">
                    <div class="aspect-[4/3] rounded-2xl bg-gradient-to-br from-sky/20 to-brand-100 overflow-hidden relative">
                        <template x-for="(image, index) in images" :key="index">
                            <img
                                x-show="currentIndex === index"
                                x-transition
                                :src="image"
                                alt="School Image"
                                class="absolute inset-0 w-full h-full object-cover rounded-2xl"
                            >
                        </template>

                        <!-- Carousel Controls -->
                        <button @click="prev"
                            class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-700 px-2 py-1 rounded-full shadow">
                            ‹
                        </button>
                        <button @click="next"
                            class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-700 px-2 py-1 rounded-full shadow">
                            ›
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission & Vision -->
        <div class="grid md:grid-cols-2 gap-8">
            <div class="rounded-3xl bg-white/80 border border-white/60 p-8 shadow-soft">
                <h2 class="font-display text-2xl font-bold text-slate-900">Our Mission</h2>
                <p class="text-slate-600 mt-4">
                    To provide quality education that empowers students with knowledge, skills, and values necessary for lifelong learning and responsible citizenship.
                </p>
            </div>
            <div class="rounded-3xl bg-white/80 border border-white/60 p-8 shadow-soft">
                <h2 class="font-display text-2xl font-bold text-slate-900">Our Vision</h2>
                <p class="text-slate-600 mt-4">
                    To be a leading institution in elementary education, fostering academic excellence, creativity, and character development in every student.
                </p>
            </div>
        </div>

        <!-- Core Values -->
        <div>
            <h2 class="font-display text-3xl font-bold text-slate-900 mb-8">Our Core Values</h2>
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Excellence -->
                <div class="rounded-2xl bg-white/80 border border-white/60 p-6 shadow-soft">
                    <div class="w-12 h-12 rounded-xl bg-berry/20 text-berry flex items-center justify-center mb-4">
                        <span class="text-2xl">🎯</span>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900">Excellence</h3>
                    <p class="text-slate-600 mt-2 text-sm">Striving for the highest standards in everything we do</p>
                </div>

                <!-- Integrity -->
                <div class="rounded-2xl bg-white/80 border border-white/60 p-6 shadow-soft">
                    <div class="w-12 h-12 rounded-xl bg-grass/20 text-grass flex items-center justify-center mb-4">
                        <span class="text-2xl">🤝</span>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900">Integrity</h3>
                    <p class="text-slate-600 mt-2 text-sm">Acting with honesty and strong moral principles</p>
                </div>

                <!-- Innovation -->
                <div class="rounded-2xl bg-white/80 border border-white/60 p-6 shadow-soft">
                    <div class="w-12 h-12 rounded-xl bg-sky/20 text-sky flex items-center justify-center mb-4">
                        <span class="text-2xl">💡</span>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900">Innovation</h3>
                    <p class="text-slate-600 mt-2 text-sm">Embracing new ideas and creative approaches to learning</p>
                </div>

                <!-- Community -->
                <div class="rounded-2xl bg-white/80 border border-white/60 p-6 shadow-soft">
                    <div class="w-12 h-12 rounded-xl bg-sun/20 text-amber-600 flex items-center justify-center mb-4">
                        <span class="text-2xl">💫</span>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900">Community</h3>
                    <p class="text-slate-600 mt-2 text-sm">Building strong relationships with families and our local area</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="rounded-3xl bg-gradient-to-br from-brand-50 to-sky/10 border border-white/60 p-8 md:p-12 shadow-soft">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="font-display text-3xl font-bold text-slate-900">Join Our Community</h2>
                <p class="text-lg text-slate-600 mt-4">
                    We're always excited to welcome new families into our school community. Learn more about enrollment or schedule a visit.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('contact') }}" class="rounded-xl bg-slate-900 text-white px-6 py-3 font-semibold hover:bg-slate-800 active:scale-[.98] transition">Contact Us</a>
                    <a href="{{ route('news') }}" class="rounded-xl bg-white/80 border border-white/70 px-6 py-3 font-semibold text-slate-800 hover:bg-white active:scale-[.98] transition">Latest News</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Dashboard Button for Logged-in Users -->
    @if(auth()->check())
        <div class="fixed bottom-6 right-6 z-50">
            <a href="{{ route('dashboard') }}" 
                class="bg-white border border-black text-black px-6 py-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 active:scale-95 text-right"
                wire:navigate>
                <span class="font-semibold text-sm">
                    Back to Dashboard
                </span>
            </a>
        </div>
    @endif
</div>
