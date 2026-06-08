<div>
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Buscador --}}
    <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <div class="flex gap-2">
            <div class="flex-1">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por nombre..."
                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 shadow-sm">
            </div>
            @if($search)
                <button type="button" wire:click="$set('search', '')" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Limpiar
                </button>
            @endif
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 relative">
                    <thead class="bg-primary dark:bg-primary-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-white uppercase tracking-wider">
                                Nombre
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-white uppercase tracking-wider">
                                Descripción
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-sm font-semibold text-white uppercase tracking-wider">
                                Etiquetas
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-sm font-semibold text-white uppercase tracking-wider">
                                Subcategorías
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-sm font-semibold text-white uppercase tracking-wider">
                                Involucrados
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-sm font-semibold text-white uppercase tracking-wider">
                                Horarios
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-sm font-semibold text-white uppercase tracking-wider">
                                Acciones
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-sm font-semibold text-white uppercase tracking-wider">
                                Desenlaces
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-white uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="sticky right-0 px-6 py-3 text-right text-sm font-semibold text-white uppercase tracking-wider bg-primary dark:bg-primary-700">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($categorias as $categoria)
                            @php $rowBg = $loop->even ? 'bg-gray-50 dark:bg-gray-750' : 'bg-white dark:bg-gray-800'; @endphp
                            <tr class="group {{ $rowBg }} hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $categoria->nombre }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $categoria->descripcion ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($categoria->etiquetas && count($categoria->etiquetas) > 0)
                                        @php
                                            $nombresEtiquetas = collect($categoria->etiquetas)->map(function($e) {
                                                return is_array($e) ? ($e['nombre'] ?? '') : $e;
                                            })->implode(', ');
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-800 dark:bg-teal-800 dark:text-teal-100" title="{{ $nombresEtiquetas }}">
                                            {{ count($categoria->etiquetas) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                        {{ $categoria->subcategorias_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                        {{ $categoria->tipos_involucrados_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                        {{ $categoria->horarios_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100">
                                        {{ $categoria->acciones_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800 dark:bg-pink-800 dark:text-pink-100">
                                        {{ $categoria->desenlaces_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($categoria->activo)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Activo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="sticky right-0 px-6 py-3 whitespace-nowrap text-right text-sm font-medium {{ $rowBg }} group-hover:bg-indigo-50 dark:group-hover:bg-gray-700 transition-colors">
                                    <a href="{{ route('categorias.edit', $categoria) }}" wire:navigate
                                       class="inline-flex items-center justify-center w-8 h-8 text-secondary hover:bg-secondary-50 dark:hover:bg-secondary-900 rounded-lg transition-colors mr-2"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Está seguro de eliminar esta categoría? Se eliminarán también todas sus subcategorías, tipos de involucrados y horarios asociados.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900 rounded-lg transition-colors"
                                                title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800">
                                    @if($search)
                                        No se encontraron categorías que coincidan con "{{ $search }}".
                                    @else
                                        No hay categorías registradas.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                {{ $categorias->links() }}
            </div>
    </div>
</div>
