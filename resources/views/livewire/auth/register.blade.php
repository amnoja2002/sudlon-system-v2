<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Concatenate first_name and last_name to create full name
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['password'] = Hash::make($validated['password']);

        // Set default role to Parent on self-registration
        $parentRoleId = optional(Role::where('slug', 'parent')->first())->id;
        if ($parentRoleId) {
            $validated['role_id'] = $parentRoleId;
        }

        // Store user data in session for email verification
        session([
            'registration_data' => $validated,
            'otp' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        // Send OTP email
        $this->sendOtpEmail($validated['email'], session('otp'));

        // Redirect to email verification
        $this->redirect(route('auth.email-verification'), navigate: true);
    }

    private function sendOtpEmail($email, $otp): void
    {
        // For now, we'll use a simple mail implementation
        // You can enhance this with proper mail templates later
        \Illuminate\Support\Facades\Mail::raw(
            "Your OTP code is: {$otp}. This code will expire in 10 minutes.",
            function ($message) use ($email) {
                $message->to($email)
                        ->subject('Email Verification - OTP Code');
            }
        );
    }
}; ?>

<div class="flex flex-col gap-6 sm:gap-8">
    <!-- Logo and Info -->
    <div class="flex flex-col items-center text-center mb-4">
        <img src="{{ asset('images/logo.jpg') }}" alt="DepEd Logo" class="h-12 w-auto mb-3 sm:h-14">
        <h1 class="text-lg font-bold text-gray-800 sm:text-xl">{{ config('app.name') }}</h1>
        <p class="text-xs text-gray-600 sm:text-sm">San Vicente, Surigao del Sur</p>
    </div>

    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- First Name -->
        <flux:input
            wire:model="first_name"
            :label="__('First Name')"
            type="text"
            required
            autofocus
            autocomplete="given-name"
            :placeholder="__('First name')"
            class="w-full"
        />

        <!-- Last Name -->
        <flux:input
            wire:model="last_name"
            :label="__('Last Name')"
            type="text"
            required
            autocomplete="family-name"
            :placeholder="__('Last name')"
            class="w-full"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
            class="w-full"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
            class="w-full"
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
            class="w-full"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full py-3 text-base font-medium">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate class="text-deped-600 hover:text-deped-700 hover:underline">
            {{ __('Log in') }}
        </flux:link>
    </div>
</div>
