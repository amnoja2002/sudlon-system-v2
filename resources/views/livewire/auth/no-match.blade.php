<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public function goHome(): void
    {
        $this->redirect(route('home'), navigate: true);
    }
}; ?>

<flux:main>
    <div class="flex flex-col gap-6 text-center">
        <div class="mx-auto w-16 h-16 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('No Student Records Found') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('We couldn\'t find any student records matching your information.') }}
            </p>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        {{ __('What to do next?') }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>{{ __('Please contact the school administration to:') }}</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>{{ __('Verify your student information') }}</li>
                            <li>{{ __('Update your contact details') }}</li>
                            <li>{{ __('Get access to your student\'s records') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <flux:button 
                wire:click="goHome" 
                variant="primary" 
                class="w-full"
            >
                {{ __('Back to Home') }}
            </flux:button>
            
            <flux:link 
                :href="route('contact')" 
                wire:navigate
                class="text-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
            >
                {{ __('Contact School') }}
            </flux:link>
        </div>
    </div>
</flux:main>
