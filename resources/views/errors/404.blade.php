<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página No Encontrada</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-16 w-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Página No Encontrada
            </h1>

            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Lo sentimos, la página que buscas no existe o ha sido movida.
            </p>

            <div class="space-y-3">
                <a href="{{ url()->previous() }}"
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-150">
                    Volver
                </a>

                <a href="{{ url('/') }}"
                   class="block w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-3 px-4 rounded-lg transition duration-150">
                    Ir al Inicio
                </a>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-500 mt-6">
                Código de error: 404 - No encontrado
            </p>
        </div>
    </div>
</body>
</html>
