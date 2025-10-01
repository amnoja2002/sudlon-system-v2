<div class="max-w-7xl mx-auto px-6 pt-10 md:pt-16 pb-20">
    <!-- Header -->
    <div class="max-w-2xl">
        <h1 class="font-display text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">Get in Touch</h1>
        <p class="text-lg text-slate-600 mt-4">
            Have questions about enrollment, programs, or anything else? We're here to help! Reach out through any of the methods below.
        </p>
    </div>

    <!-- Contact Methods Grid -->
    <div class="mt-12 grid md:grid-cols-3 gap-6 mb-16">
        <!-- Phone -->
        <div class="rounded-2xl bg-white/80 border border-white/60 p-6 shadow-soft">
            <div class="w-12 h-12 rounded-xl bg-brand-500/10 text-brand-600 flex items-center justify-center mb-4">
                <i class="fa-solid fa-phone fa-xl"></i>
            </div>
            <h3 class="font-semibold text-lg text-slate-900">Call Us</h3>
            <p class="text-slate-600 mt-2">(032) 123-4567</p>
            <p class="text-sm text-slate-500 mt-1">Mon-Fri, 8:00 AM - 4:00 PM</p>
        </div>

        <!-- Email -->
        <div class="rounded-2xl bg-white/80 border border-white/60 p-6 shadow-soft">
            <div class="w-12 h-12 rounded-xl bg-berry/10 text-berry flex items-center justify-center mb-4">
                <i class="fa-solid fa-envelope fa-xl"></i>
            </div>
            <h3 class="font-semibold text-lg text-slate-900">Email</h3>
            <p class="text-slate-600 mt-2">info@sudlon.edu.ph</p>
            <p class="text-sm text-slate-500 mt-1">We aim to respond within 24 hours</p>
        </div>

        <!-- Visit -->
        <div class="rounded-2xl bg-white/80 border border-white/60 p-6 shadow-soft">
            <div class="w-12 h-12 rounded-xl bg-grass/10 text-emerald-700 flex items-center justify-center mb-4">
                <i class="fa-solid fa-location-dot fa-xl"></i>
            </div>
            <h3 class="font-semibold text-lg text-slate-900">Visit Us</h3>
            <p class="text-slate-600 mt-2">Sudlon II, Cebu City</p>
            <p class="text-sm text-slate-500 mt-1">Schedule a tour - we'd love to show you around!</p>
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

    <!-- Contact Form Section -->
    <livewire:contact-form />

</div>