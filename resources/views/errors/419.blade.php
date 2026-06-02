<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Expirada</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-16 w-16 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Sesión Expirada
            </h1>

            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Tu sesión ha expirado por seguridad. Por favor, inicia sesión nuevamente para continuar.
            </p>

            <div class="space-y-3">
                <a href="{{ route('login') }}"
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-150">
                    Iniciar Sesión
                </a>

                <a href="{{ url('/') }}"
                   class="block w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-3 px-4 rounded-lg transition duration-150">
                    Ir al Inicio
                </a>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-500 mt-6">
                Código de error: 419 - Token CSRF inválido o expirado
            </p>
        </div>
    </div>
</body>
</html>
