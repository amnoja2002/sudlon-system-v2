<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $otp = '';
    public bool $otp_verified = false;
    public string $message = '';

    public function mount(): void
    {
        if (!session()->has('registration_data')) {
            $this->redirect(route('register'), navigate: true);
        }
    }

    public function verifyOtp(): void
    {
        $this->validate([
            'otp' => ['required', 'string', 'size:6']
        ]);

        $sessionOtp = session('otp');
        $otpExpiresAt = session('otp_expires_at');

        if (!$sessionOtp || !$otpExpiresAt || now()->isAfter($otpExpiresAt)) {
            $this->message = 'OTP has expired. Please register again.';
            session()->forget(['registration_data', 'otp', 'otp_expires_at']);
            $this->redirect(route('register'), navigate: true);
            return;
        }

        if ($this->otp !== $sessionOtp) {
            $this->message = 'Invalid OTP code. Please try again.';
            return;
        }

        // OTP is valid, proceed with registration
        $this->otp_verified = true;
        $this->completeRegistration();
    }

    private function completeRegistration(): void
    {
        $registrationData = session('registration_data');
        
        // Create a temporary user object for validation (don't save to database yet)
        $tempUser = new User($registrationData);

        // Find matching students BEFORE creating the account
        $matchingStudents = $this->findMatchingStudents($tempUser);

        if (count($matchingStudents) > 0) {
            // Matching students found - proceed with account creation
            event(new Registered(($user = User::create($registrationData))));

            // Store matching students in session for dashboard
            session(['matching_students' => $matchingStudents]);

            // Clear registration session data
            session()->forget(['registration_data', 'otp', 'otp_expires_at']);

            // Login the user
            Auth::login($user);

            // Redirect to parent dashboard with matching students
            $this->redirect(route('parent.dashboard'), navigate: true);
        } else {
            // No matching students found - don't create account, redirect to no-match page
            session()->forget(['registration_data', 'otp', 'otp_expires_at']);
            $this->redirect(route('auth.no-match'), navigate: true);
        }
    }

    private function findMatchingStudents($user): array
    {
        // First, try to match by exact email
        $studentsByEmail = Student::where(function($query) use ($user) {
            $query->where('mother_email', $user->email)
                  ->orWhere('father_email', $user->email)
                  ->orWhere('guardian_email', $user->email);
        })->get();

        if ($studentsByEmail->count() > 0) {
            return $studentsByEmail->toArray();
        }

        // If no email match, try name matching with more strict criteria
        $nameParts = explode(' ', trim($user->name));
        if (count($nameParts) >= 2) {
            $firstName = trim($nameParts[0]);
            $lastName = trim($nameParts[1]);
            
            // Only proceed if we have valid first and last names
            if (strlen($firstName) >= 2 && strlen($lastName) >= 2) {
                $studentsByName = Student::where(function($query) use ($firstName, $lastName) {
                    $query->where(function($subQuery) use ($firstName, $lastName) {
                        // Check mother's name
                        $subQuery->where('mother_first_name', 'LIKE', "%{$firstName}%")
                                 ->where('mother_last_name', 'LIKE', "%{$lastName}%");
                    })->orWhere(function($subQuery) use ($firstName, $lastName) {
                        // Check father's name
                        $subQuery->where('father_first_name', 'LIKE', "%{$firstName}%")
                                 ->where('father_last_name', 'LIKE', "%{$lastName}%");
                    })->orWhere(function($subQuery) use ($firstName, $lastName) {
                        // Check guardian's name
                        $subQuery->where('guardian_first_name', 'LIKE', "%{$firstName}%")
                                 ->where('guardian_last_name', 'LIKE', "%{$lastName}%");
                    });
                })->get();

                return $studentsByName->toArray();
            }
        }

        // No matches found
        return [];
    }

    public function resendOtp(): void
    {
        $registrationData = session('registration_data');
        if ($registrationData) {
            $newOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            session([
                'otp' => $newOtp,
                'otp_expires_at' => now()->addMinutes(10)
            ]);
            
            \Illuminate\Support\Facades\Mail::raw(
                "Your new OTP code is: {$newOtp}. This code will expire in 10 minutes.",
                function ($message) use ($registrationData) {
                    $message->to($registrationData['email'])
                            ->subject('Email Verification - New OTP Code');
                }
            );
            
            $this->message = 'A new OTP code has been sent to your email.';
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header 
        :title="__('Verify Your Email')" 
        :description="__('Enter the 6-digit code sent to your email address')" 
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    @if($message)
        <div class="p-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-md">
            {{ $message }}
        </div>
    @endif

    <form method="POST" wire:submit="verifyOtp" class="flex flex-col gap-6">
        <!-- OTP Input -->
        <flux:input
            wire:model="otp"
            :label="__('Verification Code')"
            type="text"
            required
            autofocus
            maxlength="6"
            placeholder="000000"
            class="text-center text-2xl tracking-widest"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Verify Email') }}
            </flux:button>
        </div>
    </form>

    <div class="text-center">
        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">
            {{ __('Didn\'t receive the code?') }}
        </p>
        <flux:button 
            wire:click="resendOtp" 
            variant="ghost" 
            size="sm"
            class="text-blue-600 hover:text-blue-800"
        >
            {{ __('Resend Code') }}
        </flux:button>
    </div>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Wrong email?') }}</span>
        <flux:link :href="route('register')" wire:navigate>{{ __('Start over') }}</flux:link>
    </div>
</div>
