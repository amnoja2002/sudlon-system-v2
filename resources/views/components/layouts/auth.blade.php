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
    <!-- Government Header -->
    <div class="bg-deped-600 text-white">
        <div class="max-w-7xl mx-auto px-4 py-1 sm:px-6 lg:px-8">
            <div class="flex justify-between text-xs">
                <a href="https://www.gov.ph" class="hover:text-gold-300">GOVPH</a>
                <a href="https://www.deped.gov.ph" class="hover:text-gold-300">Department of Education</a>
            </div>
        </div>
    </div>

    <div class="min-h-[calc(100vh-40px)] flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
        
        <!-- Footer -->
        <div class="w-full mt-8 px-4 py-2 text-center">
            <p class="text-xs text-gray-600">
                © {{ date('Y') }} Department of Education - Division of Surigao del Sur. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
