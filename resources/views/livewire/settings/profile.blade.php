<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;
    public string $name = '';
    public string $email = '';
    public $photo;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle photo upload
        if ($this->photo) {
            // Delete old photo if exists
            if ($user->photo && \Storage::disk('public')->exists($user->photo)) {
                \Storage::disk('public')->delete($user->photo);
            }

            // Store new photo
            $validated['photo'] = $this->photo->store('profile-photos', 'public');
        } else {
            unset($validated['photo']);
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<div class="bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-8">
                <div class="flex items-center space-x-6">
                    <!-- Profile Avatar -->
                    <div class="relative">
                        @if(auth()->user()->photo)
                            <img src="{{ auth()->user()->profile_photo_url() }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="h-24 w-24 rounded-full object-cover shadow-lg border-4 border-white">
                        @else
                            <img src="{{ auth()->user()->profile_photo_url() }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="h-24 w-24 rounded-full object-cover shadow-lg border-4 border-white">
                        @endif
                        <div class="absolute -bottom-2 -right-2 h-8 w-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Profile Info -->
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
                        <p class="text-lg text-gray-600 mt-1">{{ auth()->user()->email }}</p>
                        <div class="flex items-center mt-3 space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                {{ auth()->user()->role?->name ?? 'User' }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Verified Account
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Settings Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Profile Information</h2>
                <p class="text-gray-600 mt-1">Update your personal information and account settings</p>
            </div>

            <form wire:submit="updateProfileInformation" class="p-6 space-y-6" enctype="multipart/form-data">
                <!-- Photo Upload Field -->
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <img src="{{ auth()->user()->profile_photo_url() }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="h-20 w-20 rounded-full object-cover border-4 border-gray-200">
                        </div>
                        <div class="flex-1">
                            <input type="file" 
                                   id="photo"
                                   wire:model="photo" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                   accept="image/*">
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            @error('photo') 
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="name"
                               wire:model="name" 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors dark:text-black"
                               placeholder="Enter your full name"
                               required>
                    </div>
                    @error('name') 
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
            <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" 
                               id="email"
                               wire:model="email" 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors dark:text-black"
                               placeholder="Enter your email address"
                               required>
                    </div>
                    @error('email') 
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Email Verification Status -->
                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Email Verification Required</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Your email address is not verified. Please check your email for a verification link.</p>
                                        <button type="button" 
                                                wire:click.prevent="resendVerificationNotification"
                                                class="mt-2 text-sm font-medium text-yellow-800 hover:text-yellow-900 underline">
                                            Resend verification email
                                        </button>
                                    </div>
                                    @if (session('status') === 'verification-link-sent')
                                        <div class="mt-2 text-sm font-medium text-green-600">
                                            A new verification link has been sent to your email address.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Account Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">
                            @if(auth()->user()->role?->slug === 'teacher')
                                {{ auth()->user()->classrooms->count() }}
                            @elseif(auth()->user()->role?->slug === 'principal')
                                {{ \App\Models\User::count() }}
                            @else
                                {{ auth()->user()->created_at->diffInDays(now()) }}
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">
                            @if(auth()->user()->role?->slug === 'teacher')
                                Classrooms
                            @elseif(auth()->user()->role?->slug === 'principal')
                                Total Users
                            @else
                                Days Active
                            @endif
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ auth()->user()->created_at->format('M Y') }}
                        </div>
                        <div class="text-sm text-gray-600">Member Since</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ auth()->user()->updated_at->diffInDays(now()) }}
                        </div>
                        <div class="text-sm text-gray-600">Days Since Update</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-200 gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>
                        
                        <x-action-message class="text-green-600 font-medium" on="profile-updated">
                            <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Profile updated successfully!
                        </x-action-message>
                    </div>

                    <div class="flex items-center justify-center sm:justify-end">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </form>
        </div>

        

        <!-- Danger Zone -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-red-200">
            <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                <h3 class="text-lg font-semibold text-red-900">Danger Zone</h3>
                <p class="text-sm text-red-700">Irreversible and destructive actions</p>
            </div>
            <div class="p-6">
        <livewire:settings.delete-user-form />
            </div>
        </div>
    </div>
</div>
