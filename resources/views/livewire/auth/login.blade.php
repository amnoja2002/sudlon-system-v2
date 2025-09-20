<div class="min-h-screen flex flex-col justify-center px-4 sm:px-6 lg:px-8 bg-white">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <!-- Logo and Info -->
        <a class="mt-6 flex flex-col items-center text-center" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.jpg') }}" alt="DepEd Logo" class="h-14 w-auto mb-2">
            <h1 class="text-base font-bold text-gray-800">{{ config('app.name') }}</h1>
            <p class="text-xs text-gray-600">San Vicente, Surigao del Sur</p>
        </a>

        <!-- Header Component -->
        <x-auth-header
            :title="__('Log in to your account')"
            :description="__('Enter your email and password below to log in')" />
    </div>

    <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="bg-white py-6 px-4 shadow rounded-lg sm:px-6">
            <!-- Session Status -->
            <x-auth-session-status class="mb-3 text-center" :status="session('status')" />

            <!-- Login Form -->
            <form method="POST" wire:submit.prevent="login" class="space-y-5">
                @csrf

                <!-- Email -->
                <flux:input
                    wire:model="email"
                    :label="__('Email address')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                />

                <!-- Password Field with Eye Toggle -->
                <div class="space-y-2 relative">
                    <flux:input
                        wire:model="password"
                        :label="__('Password')"
                        id="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="pr-10"
                    />

                    <!-- Eye Toggle Button -->
                    <button type="button"
                        id="togglePassword"
                        class="absolute right-3 top-9 text-gray-500 hover:text-gray-700 focus:outline-none"
                        aria-label="Toggle password visibility">
                        <!-- Eye Icon -->
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>

                        <!-- Eye Off Icon -->
                        <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3.98 8.223A10.477 10.477 0 002.458 12c1.274 4.057 5.065 7 9.542 7a9.956 9.956 0 004.478-1.06M6.517 6.517A9.958 9.958 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.478 10.478 0 01-4.17 5.477M6.517 6.517L3 3m3.517 3.517L3 3m0 0l18 18"/>
                        </svg>
                    </button>

                    @error('password')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me + Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox"
                            wire:model="remember"
                            id="remember"
                            class="rounded border-gray-300 text-deped-600 focus:ring-deped-500" />
                        <label for="remember" class="ml-2 block text-sm text-gray-600">
                            {{ __('Remember me') }}
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm text-deped-600 hover:text-deped-700 hover:underline">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="w-full flex justify-center items-center gap-2 px-4 py-2 bg-deped-600 text-black font-semibold rounded-md hover:bg-deped-700 active:scale-95 focus:outline-none focus:ring-2 focus:ring-deped-500 focus:ring-offset-2 transition-all">
                        <span wire:loading.remove wire:target="login">
                            {{ __('Log in') }}
                        </span>
                        <span wire:loading wire:target="login" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                                </path>
                            </svg>
                            {{ __('Logging in...') }}
                        </span>
                    </button>
                </div>
            </form>

            <!-- Register Link -->
            @if (Route::has('register'))
                <div class="mt-5 text-center">
                    <span class="text-sm text-gray-600">{{ __("Don't have an account?") }}</span>
                    <a href="{{ route('register') }}"
                       class="ml-1 text-sm text-deped-600 hover:text-deped-700 hover:underline">
                        {{ __('Sign up') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- JS for toggling eye icons -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.getElementById("togglePassword");
            const passwordInput = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");
            const eyeOffIcon = document.getElementById("eyeOffIcon");

            toggleBtn.addEventListener("click", function () {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);

                eyeIcon.classList.toggle("hidden");
                eyeOffIcon.classList.toggle("hidden");
            });
        });
    </script>
</div>
