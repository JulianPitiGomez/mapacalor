<div>
    {{-- Filtros --}}
    <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtros</h3>
            @if($search || $filterCategoria || $filterBarrio || $filterFechaDesde || $filterFechaHasta || collect($filtrosEtiquetas)->filter()->isNotEmpty())
                <button wire:click="limpiarFiltros" class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                    Limpiar filtros
                </button>
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                <input type="text" wire:model.live="search" placeholder="ID o Observaciones..."
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoría</label>
                <select wire:model.live="filterCategoria"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                    <option value="">Todas</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barrio</label>
                <select wire:model.live="filterBarrio"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                    <option value="">Todos</option>
                    @foreach($barrios as $barrio)
                        <option value="{{ $barrio->id }}">{{ $barrio->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Desde</label>
                <input type="date" wire:model.live="filterFechaDesde"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
            </div>
        </div>

        {{-- Filtros de etiquetas dinámicas --}}
        @if(!empty($etiquetasCategoria))
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Filtrar por etiquetas de la categoría
            </label>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($etiquetasCategoria as $etiqueta)
                    @php
                        // Compatibilidad con formato antiguo (string) y nuevo (objeto)
                        $esObjeto = is_array($etiqueta);
                        $nombre = $esObjeto ? ($etiqueta['nombre'] ?? '') : $etiqueta;
                        $tipo = $esObjeto ? ($etiqueta['tipo'] ?? 'texto') : 'texto';
                        $opciones = $esObjeto ? ($etiqueta['opciones'] ?? []) : [];
                        $clave = Str::slug($nombre, '_');
                    @endphp
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $nombre }}</label>
                        @if($tipo === 'select' && !empty($opciones))
                            <select wire:model.live="filtrosEtiquetas.{{ $clave }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm">
                                <option value="">Todos</option>
                                @foreach($opciones as $opcion)
                                    <option value="{{ $opcion }}">{{ $opcion }}</option>
                                @endforeach
                            </select>
                        @elseif($tipo === 'fecha')
                            <input type="date" wire:model.live="filtrosEtiquetas.{{ $clave }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm">
                        @elseif($tipo === 'numero')
                            <input type="number" wire:model.live.debounce.500ms="filtrosEtiquetas.{{ $clave }}"
                                placeholder="Buscar {{ strtolower($nombre) }}..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm">
                        @else
                            <input type="text" wire:model.live.debounce.500ms="filtrosEtiquetas.{{ $clave }}"
                                placeholder="Buscar {{ strtolower($nombre) }}..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Tabla --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 relative">
                    <thead class="bg-primary dark:bg-primary-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white uppercase tracking-wider">Subcategoría</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white uppercase tracking-wider">Barrio</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white uppercase tracking-wider">Usuario</th>
                            <th class="sticky right-0 px-6 py-3 text-right text-sm font-semibold text-white uppercase tracking-wider bg-primary dark:bg-primary-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($hechos as $hecho)
                            @php $rowBg = $loop->even ? 'bg-gray-50 dark:bg-gray-750' : 'bg-white dark:bg-gray-800'; @endphp
                            <tr class="group {{ $rowBg }} hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">#{{ $hecho->id }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $hecho->fecha_hecho->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $hecho->categoria->nombre }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $hecho->subcategoria->nombre ?? '-' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $hecho->barrio->nombre ?? '-' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $hecho->user->name }}</td>
                                <td class="sticky right-0 px-6 py-3 whitespace-nowrap text-right text-sm font-medium {{ $rowBg }} group-hover:bg-indigo-50 dark:group-hover:bg-gray-700 transition-colors">
                                    <a href="{{ route('hechos.edit', $hecho) }}" wire:navigate
                                       class="inline-flex items-center justify-center w-8 h-8 text-secondary hover:bg-secondary-50 dark:hover:bg-secondary-900 rounded-lg transition-colors mr-2"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button wire:click="deleteHecho({{ $hecho->id }})"
                                        onclick="return confirm('¿Está seguro de eliminar este hecho?')"
                                        class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900 rounded-lg transition-colors"
                                        title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800">
                                    No se encontraron hechos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                {{ $hechos->links() }}
            </div>
    </div>
</div>
