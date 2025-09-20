<div class="max-w-6xl mx-auto px-6 pt-12 md:pt-16 pb-20">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="font-display text-3xl md:text-4xl font-extrabold text-slate-900">Parent Portal</h2>
            <p class="text-slate-600 mt-1">A friendly snapshot of your child's day.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('home') }}" class="rounded-xl bg-slate-900 text-white px-4 py-2 font-semibold hover:bg-slate-800 transition">Back to Home</a>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mt-8">
        <div class="rounded-2xl bg-white/85 border border-white/60 p-5 shadow-soft">
            <p class="text-xs font-semibold text-slate-500">Today</p>
            <p class="text-lg font-bold text-slate-900 mt-1">Attendance: Present</p>
            <p class="text-sm text-slate-700 mt-2">Arrival at 8:12 AM</p>
        </div>
        <div class="rounded-2xl bg-white/85 border border-white/60 p-5 shadow-soft">
            <p class="text-xs font-semibold text-slate-500">Homework</p>
            <ul class="mt-3 space-y-2 text-sm text-slate-700">
                <li>Math worksheet • Due Thu</li>
                <li>Read 15 minutes • Daily</li>
            </ul>
        </div>
        <div class="rounded-2xl bg-white/85 border border-white/60 p-5 shadow-soft">
            <p class="text-xs font-semibold text-slate-500">Messages</p>
            <p class="text-sm text-slate-700 mt-3">New note from Ms. Lee: "Great effort during reading circle!"</p>
        </div>
    </div>
</div>