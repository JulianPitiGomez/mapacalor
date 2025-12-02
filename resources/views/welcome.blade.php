<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Observatorio de Seguridad - Municipalidad de Mercedes</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <div class="min-h-screen flex flex-col">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <x-application-logo class="block h-12 w-auto" />
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Observatorio de Seguridad</h1>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Municipalidad de Mercedes</p>
                            </div>
                        </div>
                        @if (Route::has('login'))
                            <div>
                                @auth
                                    <a href="{{ url('/estadisticas') }}" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-600 text-white font-semibold rounded-lg shadow-md transition duration-150">
                                        Ir al Panel
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-600 text-white font-semibold rounded-lg shadow-md transition duration-150">
                                        Iniciar Sesión
                                    </a>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
                <div class="max-w-6xl w-full">
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <!-- Left Column - Content -->
                        <div class="space-y-6">
                            <div class="space-y-4">
                                <h2 class="text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                                    Sistema de Gestión y Análisis de Datos de Seguridad
                                </h2>
                                <p class="text-lg text-gray-600 dark:text-gray-300">
                                    Herramienta integral para el registro, seguimiento y análisis estadístico de eventos relacionados con la seguridad ciudadana en Mercedes.
                                </p>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Estadísticas en Tiempo Real</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Visualización de datos mediante gráficos y mapas de calor interactivos.</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Registro de Hechos</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Sistema completo para el registro y seguimiento de eventos de seguridad.</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Geolocalización</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Ubicación precisa de eventos con mapas interactivos integrados.</p>
                                    </div>
                                </div>
                            </div>

                            @guest
                                <div class="pt-4">
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 bg-primary hover:bg-primary-600 text-white font-bold text-lg rounded-lg shadow-lg transition duration-150 transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                        Acceder al Sistema
                                    </a>
                                </div>
                            @endguest
                        </div>

                        <!-- Right Column - Image/Visual -->
                        <div class="relative">
                            <div class="aspect-square rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 dark:from-primary/10 dark:to-secondary/10 flex items-center justify-center overflow-hidden shadow-2xl">
                                <!-- Aquí podrías agregar una imagen representativa de Mercedes -->
                                <div class="text-center p-8">
                                    <div class="mb-6">
                                        <svg class="w-48 h-48 mx-auto text-primary/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                        </svg>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-3xl font-bold text-gray-700 dark:text-gray-300">Mercedes</p>
                                        <p class="text-xl text-gray-600 dark:text-gray-400">Buenos Aires, Argentina</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Decorative Elements -->
                            <div class="absolute -top-4 -right-4 w-24 h-24 bg-primary/10 rounded-full blur-3xl"></div>
                            <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-secondary/10 rounded-full blur-3xl"></div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            © {{ date('Y') }} Municipalidad de Mercedes. Todos los derechos reservados.
                        </div>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 dark:text-gray-400">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Sistema Seguro
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                v1.0
                            </span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
