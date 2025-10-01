@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center space-y-2">
    <flux:heading size="xl" class="text-xl sm:text-2xl font-bold">{{ $title }}</flux:heading>
    <flux:subheading class="text-sm sm:text-base text-gray-600">{{ $description }}</flux:subheading>
</div>
