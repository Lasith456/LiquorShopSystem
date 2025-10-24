<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'NsoftItSolutions') }}</title>

    {{-- ✅ Tailwind + Alpine --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- ✅ Font Awesome (optional) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <style>
        [x-cloak] { display: none !important; }

        /* Scrollbar styling */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Sidebar scroll */
        .sub-menu-content::-webkit-scrollbar { width: 8px; }
        .sub-menu-content::-webkit-scrollbar-track { background: #e5e7eb; border-radius: 10px; }
        .sub-menu-content::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 10px; }
        .sub-menu-content::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</head>
<body class="h-full font-sans antialiased">
<div 
    x-data="{
        sidebarOpen: false,
        sidebarHover: false,
        sidebarCollapsed: JSON.parse(localStorage.getItem('sidebarCollapsed')) || false,
        init() {
            this.$watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', JSON.stringify(value)))
        }
    }"
    @keydown.window.escape="sidebarOpen = false">

    @auth
        {{-- ✅ Mobile Sidebar --}}
        <div x-cloak class="relative z-50 lg:hidden" role="dialog" aria-modal="true" x-show="sidebarOpen">
            <div x-show="sidebarOpen" class="fixed inset-0 bg-gray-900/80" x-transition></div>
            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen" class="relative mr-16 flex w-full max-w-xs flex-1 transition-transform transform"
                     x-transition:enter="duration-300 ease-out" 
                     x-transition:enter-start="-translate-x-full" 
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="duration-300 ease-in" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="-translate-x-full">

                    {{-- Sidebar Content --}}
                    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4 no-scrollbar">
                        <div class="flex h-16 items-center">
                            <a href="/home" class="text-white font-bold text-lg">NSoft Stock Management</a>
                        </div>
                        @include('layouts.navigation')
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ Desktop Sidebar --}}
        <div 
            @mouseenter="sidebarHover = true" 
            @mouseleave="sidebarHover = false"
            class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:flex-col transition-all duration-300"
            :class="{ 'lg:w-16': sidebarCollapsed && !sidebarHover, 'lg:w-72': !sidebarCollapsed || sidebarHover }">

            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-4 pb-4 no-scrollbar">
                <div class="flex h-16 items-center justify-between">
                    <a href="/home" class="text-white font-bold text-lg">
                        <span x-show="!sidebarCollapsed || sidebarHover">NSoftStock</span>
                        <span x-show="sidebarCollapsed && !sidebarHover">NS</span>
                    </a>
                </div>
                @include('layouts.navigation')
            </div>

            {{-- Sidebar Collapse Button --}}
            <div class="border-t border-gray-700 p-2 bg-gray-900">
                <button 
                    @click="sidebarCollapsed = !sidebarCollapsed"
                    class="w-full flex items-center justify-center p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-md transition">
                    <svg x-show="!sidebarCollapsed" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    <svg x-show="sidebarCollapsed" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- ✅ Main Layout (Content + Top Navbar) --}}
        <div 
            class="transition-all duration-300"
            :class="{ 'lg:pl-16': sidebarCollapsed && !sidebarHover, 'lg:pl-72': !sidebarCollapsed || sidebarHover }">

            {{-- Top Navbar --}}
            <div class="sticky top-0 z-40 flex h-12 items-center gap-x-4 border-b bg-white dark:bg-gray-800 px-4 shadow-sm sm:px-6 lg:px-8">
                <button type="button" class="lg:hidden p-2 text-gray-700 dark:text-gray-400" @click="sidebarOpen = true">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div class="flex flex-1 justify-end">
                    {{-- User Menu --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2">
                            <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" alt="">
                            <span class="hidden lg:block text-gray-700 dark:text-gray-200 font-semibold">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04L10.54 13a.75.75 0 01-1.08 0L5.25 8.27a.75.75 0 01-.02-1.06z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute right-0 mt-2 w-36 rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-900/10">
                            <!-- <a href="#" class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a> -->
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <main class="p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    @else
        {{-- ✅ Guest Layout (Login/Register Pages) --}}
        <main class="min-h-screen flex items-center justify-center bg-gray-100">
            @yield('content')
        </main>
    @endauth
</div>
<!-- ✅ Axios setup for CSRF -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = 
        document.querySelector('meta[name="csrf-token"]').getAttribute('content');
</script>

</body>
</html>
