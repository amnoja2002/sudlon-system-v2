<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public routes
Route::get('/', \App\Livewire\Home::class)->name('home');
Route::get('/about', \App\Livewire\About::class)->name('about');
Route::get('/news', \App\Livewire\News::class)->name('news');
Route::get('/contact', \App\Livewire\Contact::class)->name('contact');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Generic dashboard route that redirects based on user role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->role?->slug === 'principal') {
            return redirect()->route('principal.dashboard');
        } elseif ($user->role?->slug === 'teacher') {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->role?->slug === 'parent') {
            return redirect()->route('parent.dashboard');
        }
        
        // Default fallback
        return redirect()->route('home');
    })->name('dashboard');
    
    // Principal Dashboard
    Route::get('/principal/dashboard', \App\Livewire\Roles\Principal\Dashboard::class)
        ->middleware('role:principal')
        ->name('principal.dashboard');
    
    // Student Management
    Route::get('/principal/students', \App\Livewire\StudentManagement::class)
        ->middleware('role:principal')
        ->name('principal.students');
    
    // Teacher Dashboard
    Route::get('/teacher/dashboard', \App\Livewire\Roles\Teacher\Dashboard::class)
        ->middleware('role:teacher')
        ->name('teacher.dashboard');
    
    // Parent Dashboard
    Volt::route('parent/dashboard', 'roles.parent.dashboard')->name('parent.dashboard');
    
    // Settings routes
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    // Removed appearance settings route (unused)
});

require __DIR__.'/auth.php';
