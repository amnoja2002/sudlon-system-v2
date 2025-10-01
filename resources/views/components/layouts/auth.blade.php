<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' : '' }}{{ config('app.name', 'Sudlon Elementary School') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6">
        <div class="w-full max-w-md px-4 sm:px-6 py-6 sm:py-8 bg-white shadow-lg overflow-hidden rounded-lg sm:rounded-xl">
            {{ $slot }}
        </div>
        
        <!-- Footer -->
        <div class="w-full max-w-md mt-6 sm:mt-8 px-4 py-2 text-center">
            <p class="text-xs text-gray-600">
                © {{ date('Y') }} Department of Education - Division of Surigao del Sur. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
