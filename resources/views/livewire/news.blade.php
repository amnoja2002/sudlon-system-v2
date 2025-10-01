<div class="max-w-7xl mx-auto px-6 pt-10 md:pt-16 pb-20">
    <!-- Header -->
    <div>
        <h1 class="font-display text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">School News</h1>
        <p class="text-lg text-slate-600 mt-4">Stay up to date with the latest announcements, events, and updates from our school.</p>
    </div>

    <!-- News Grid -->
    <div class="mt-12 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($news as $item)
        <article class="group rounded-3xl bg-white/80 border border-white/60 p-6 shadow-soft hover:-translate-y-1 transition">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="px-2 py-0.5 rounded-full bg-{{ $item['category_color'] }}/10 text-{{ $item['text_color'] }} font-semibold">{{ $item['category'] }}</span>
                <span>{{ $item['date']->format('M d, Y') }}</span>
            </div>
            <h3 class="mt-4 font-semibold text-xl text-slate-900 group-hover:text-{{ $item['text_color'] }} transition">{{ $item['title'] }}</h3>
            <p class="text-slate-600 mt-2">{{ $item['excerpt'] }}</p>
            <button class="mt-4 inline-flex items-center gap-2 text-brand-700 hover:text-brand-900 font-semibold link-underline">
                Read more
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </article>
        @endforeach
    </div>

    <!-- Subscribe section -->
    <div class="mt-16 rounded-3xl bg-gradient-to-br from-brand-50 to-sky/10 border border-white/60 p-8 md:p-12 shadow-soft">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="font-display text-3xl font-bold text-slate-900">Stay Informed</h2>
            <p class="text-lg text-slate-600 mt-4">
                Get the latest updates delivered directly to your inbox by subscribing to our newsletter.
            </p>
            <form class="mt-8 flex flex-wrap justify-center gap-4">
                <input type="email" placeholder="Enter your email" class="rounded-xl border border-white/70 bg-white/90 px-4 py-3 shadow-soft focus:outline-none focus:ring-2 focus:ring-brand-400 min-w-[280px]">
                <button type="submit" class="rounded-xl bg-slate-900 text-white px-6 py-3 font-semibold hover:bg-slate-800 active:scale-[.98] transition">
                    Subscribe
                </button>
            </form>
            <p class="text-sm text-slate-500 mt-4">We respect your privacy. Unsubscribe at any time.</p>
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