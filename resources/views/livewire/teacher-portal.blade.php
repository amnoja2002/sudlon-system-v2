<div class="max-w-6xl mx-auto px-6 pt-12 md:pt-16 pb-20">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="font-display text-3xl md:text-4xl font-extrabold text-slate-900">Staff Portal</h2>
            <p class="text-slate-600 mt-1">Quick snapshots for classrooms and announcements.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('home') }}" class="rounded-xl bg-slate-900 text-white px-4 py-2 font-semibold hover:bg-slate-800 transition">Back to Home</a>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mt-8">
        <div class="rounded-2xl bg-white/85 border border-white/60 p-5 shadow-soft">
            <p class="text-xs font-semibold text-slate-500">Today</p>
            <p class="text-lg font-bold text-slate-900 mt-1">3 Upcoming Classes</p>
            <ul class="mt-3 space-y-2 text-sm text-slate-700">
                <li>08:30 • Grade 2 • Math</li>
                <li>10:00 • Grade 3 • Reading</li>
                <li>11:15 • Grade 4 • Science</li>
            </ul>
        </div>
        <div class="rounded-2xl bg-white/85 border border-white/60 p-5 shadow-soft">
            <p class="text-xs font-semibold text-slate-500">Announcements</p>
            <ul class="mt-3 space-y-2 text-sm text-slate-700">
                <li>Staff meeting Friday 3 PM</li>
                <li>Field trip forms due next week</li>
            </ul>
        </div>
        <div class="rounded-2xl bg-white/85 border border-white/60 p-5 shadow-soft">
            <p class="text-xs font-semibold text-slate-500">Quick Actions</p>
            <div class="mt-3 grid grid-cols-2 gap-3">
                <button class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 font-semibold transition">Post Update</button>
                <button class="rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 font-semibold transition">View Roster</button>
            </div>
        </div>
    </div>
</div>