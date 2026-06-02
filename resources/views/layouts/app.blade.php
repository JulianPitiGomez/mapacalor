<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo-muni-M.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js cloak style -->
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900" x-data="{ sidebarOpen: true }">
            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform duration-300 ease-in-out shadow-lg lg:translate-x-0"
                   style="background-color: #314158;">

                <!-- Logo y Nombre del Sistema -->
                <div class="flex items-center justify-between h-16 px-6"
                     style="background-color: #3F516A; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-3">
                        <x-application-logo class="block h-9 w-auto flex-shrink-0" />
                        <div class="flex flex-col">
                            <span class="text-white font-semibold text-sm leading-tight">Observatorio de</span>
                            <span class="text-white font-semibold text-sm leading-tight">Seguridad</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-gray-300 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="px-4 py-6 space-y-2 overflow-y-auto h-[calc(100vh-8rem)]">
                    <a href="{{ route('estadisticas') }}" wire:navigate
                       class="flex items-center px-4 py-3 text-gray-200 rounded-lg transition-colors {{ request()->routeIs('estadisticas') || request()->routeIs('dashboard') ? 'bg-primary font-semibold text-white' : 'hover:bg-secondary hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Estadísticas
                    </a>

                    <a href="{{ route('hechos.index') }}" wire:navigate
                       class="flex items-center px-4 py-3 text-gray-200 rounded-lg transition-colors {{ request()->routeIs('hechos.*') ? 'bg-primary font-semibold text-white' : 'hover:bg-secondary hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Hechos
                    </a>

                    <a href="{{ route('categorias.index') }}" wire:navigate
                       class="flex items-center px-4 py-3 text-gray-200 rounded-lg transition-colors {{ request()->routeIs('categorias.*') ? 'bg-primary font-semibold text-white' : 'hover:bg-secondary hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Categorías
                    </a>

                    <a href="{{ route('barrios.index') }}" wire:navigate
                       class="flex items-center px-4 py-3 text-gray-200 rounded-lg transition-colors {{ request()->routeIs('barrios.*') ? 'bg-primary font-semibold text-white' : 'hover:bg-secondary hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Barrios
                    </a>

                    @if(auth()->user()->es_supervisor)
                    <!-- Separador -->
                    <div class="my-4 border-t border-gray-600"></div>

                    <a href="{{ route('operativos.index') }}" wire:navigate
                       class="flex items-center px-4 py-3 text-gray-200 rounded-lg transition-colors {{ request()->routeIs('operativos.*') ? 'bg-primary font-semibold text-white' : 'hover:bg-secondary hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Operativos
                    </a>

                    <a href="{{ route('grupos.index') }}" wire:navigate
                       class="flex items-center px-4 py-3 text-gray-200 rounded-lg transition-colors {{ request()->routeIs('grupos.*') ? 'bg-primary font-semibold text-white' : 'hover:bg-secondary hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Grupos
                    </a>

                    <a href="{{ route('usuarios.index') }}" wire:navigate
                       class="flex items-center px-4 py-3 text-gray-200 rounded-lg transition-colors {{ request()->routeIs('usuarios.*') ? 'bg-primary font-semibold text-white' : 'hover:bg-secondary hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Usuarios
                    </a>
                    @endif
                </nav>

                <!-- User Menu -->
                <div class="absolute bottom-0 left-0 right-0 p-4"
                     style="background-color: #3F516A; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="flex items-center w-full px-4 py-3 text-gray-200 rounded-lg hover:bg-secondary hover:text-white transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="flex-1 text-left truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-cloak
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute bottom-full left-0 right-0 mb-2 rounded-lg shadow-lg"
                             style="background-color: #3F516A; border: 1px solid rgba(255, 255, 255, 0.1);">
                            <a href="{{ route('profile.edit') }}"
                               class="block px-4 py-2 text-sm text-gray-200 hover:bg-secondary hover:text-white rounded-t-lg transition-colors">
                                Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-secondary hover:text-white rounded-b-lg transition-colors">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Overlay para móvil -->
            <div x-show="sidebarOpen"
                 @click="sidebarOpen = false"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-30 bg-gray-900 bg-opacity-50 lg:hidden"
                 style="display: none;"></div>

            <!-- Main Content -->
            <div class="lg:ml-64 transition-all duration-300 ease-in-out">
                <!-- Top Bar -->
                <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-20">
                    <div class="flex items-center px-4 py-4">
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 lg:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        @isset($header)
                            <div class="flex-1">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Scripts Stack -->
        @stack('scripts')
    </body>
</html>
