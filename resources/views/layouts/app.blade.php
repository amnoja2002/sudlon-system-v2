<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DepEd - Sudlon Elementary School') }}</title>
    <meta name="description" content="Official website of Sudlon Elementary School San Vicente, under the Department of Education, Republic of the Philippines.">
    
    <!-- Viewport for responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Mobile web app capable -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    @include('partials.head')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Simple underline swoop for nav */
        .link-underline {
            background-image: linear-gradient(currentColor, currentColor);
            background-size: 0% 2px;
            background-repeat: no-repeat;
            background-position: left bottom;
            transition: background-size .25s ease;
        }
        .link-underline:hover { background-size: 100% 2px; }

        /* Gentle float for hero illustrations */
        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .floaty { animation: floatY 6s ease-in-out infinite; }

        /* Responsive Typography */
        @media (max-width: 640px) {
            .font-display { font-size: 90%; }
            body { font-size: 95%; }
        }

        /* Responsive Padding */
        @media (max-width: 768px) {
            .container { padding-left: 1rem; padding-right: 1rem; }
        }

        /* Mobile Menu */
        .mobile-menu {
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%);
        }
        .mobile-menu.active {
            transform: translateX(0);
        }

        /* Tablet Optimizations */
        @media (min-width: 768px) and (max-width: 1024px) {
            .md\:grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
            .md\:grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        }

        /* Touch Device Optimizations */
        @media (hover: none) {
            .link-underline {
                background-size: 100% 2px;
                opacity: 0.8;
            }
            .link-underline:active {
                opacity: 1;
            }
        }

        /* Print Styles */
        @media print {
            .no-print { display: none; }
            .print-break-inside-avoid { break-inside: avoid; }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-100 via-white to-gray-50 text-gray-700 font-body">

    <!-- Top bar -->
    @livewire('navigation-menu')

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-6 py-12 grid md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <img src="{{ asset('images/logo.jpg') }}" alt="DepEd Logo" class="h-16 mb-4 bg-white p-2 rounded">
                <h3 class="font-display font-bold text-xl mb-2">Sudlon Elementary School</h3>
                <p class="text-gray-400">Department of Education</p>
                <p class="text-gray-400">Republic of the Philippines</p>
            </div>
            <div>
                <h4 class="font-semibold text-gold-300 mb-4">Contact Us</h4>
                <ul class="space-y-2 text-gray-400">
                    <li>San Vicente, Barobo, Surigao del Sur</li>
                    <li>Phone: (086) 123-4567</li>
                    <li>Email: sudlones@deped.gov.ph</li>
                    <li>Office Hours: Mon–Fri, 8:00 AM–4:00 PM</li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gold-300 mb-4">Quick Links</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#about" class="hover:text-white">About Us</a></li>
                    <li><a href="#programs" class="hover:text-white">Programs</a></li>
                    <li><a href="#enrollment" class="hover:text-white">Enrollment</a></li>
                    <li><a href="#transparency" class="hover:text-white">Transparency Seal</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700">
            <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-400">© {{ date('Y') }} Department of Education - Sudlon Elementary School. All rights reserved.</p>
                <div class="flex gap-4 text-sm">
                    <a href="#privacy" class="text-gray-400 hover:text-white">Privacy Policy</a>
                    <a href="#terms" class="text-gray-400 hover:text-white">Terms of Use</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast -->
    <div id="toast" x-data="{ show: false, message: '' }" 
         x-show="show" 
         x-transition
         x-on:notify.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 2200)"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60]">
        <div class="rounded-xl bg-slate-900 text-white px-4 py-3 shadow-lg" x-text="message"></div>
    </div>

    @livewireScripts
    @push('scripts')
</body>
</html>