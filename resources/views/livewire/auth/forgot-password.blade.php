<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('A reset link will be sent if the account exists.'));
    }
}; ?>

<div class="flex flex-col gap-6 sm:gap-8">
    <!-- Logo and Info -->
    <div class="flex flex-col items-center text-center mb-4">
        <img src="{{ asset('images/logo.jpg') }}" alt="DepEd Logo" class="h-12 w-auto mb-3 sm:h-14">
        <h1 class="text-lg font-bold text-gray-800 sm:text-xl">{{ config('app.name') }}</h1>
        <p class="text-xs text-gray-600 sm:text-sm">San Vicente, Surigao del Sur</p>
    </div>

    <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset link')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email Address')"
            type="email"
            required
            autofocus
            placeholder="email@example.com"
            class="w-full"
        />

        <flux:button variant="primary" type="submit" class="w-full py-3 text-base font-medium">
            {{ __('Email password reset link') }}
        </flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
        <span>{{ __('Or, return to') }}</span>
        <flux:link :href="route('login')" wire:navigate class="text-deped-600 hover:text-deped-700 hover:underline">
            {{ __('log in') }}
        </flux:link>
    </div>
</div>
