<div>
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                <input type="text" wire:model.live="search" placeholder="Nombre del grupo..."
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Departamento</label>
                <select wire:model.live="filterDepartamento"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                    <option value="">Todos</option>
                    @foreach($departamentos as $departamento)
                        <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Listado de Grupos --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            {{-- Encabezado --}}
            <div class="hidden md:grid md:grid-cols-12 gap-2 px-4 py-3 bg-primary dark:bg-primary-700 text-sm font-semibold text-white uppercase tracking-wider">
                <div class="col-span-1">ID</div>
                <div class="col-span-3">Nombre</div>
                <div class="col-span-3">Departamento</div>
                <div class="col-span-2">Encargado</div>
                <div class="col-span-1">Miembros</div>
                <div class="col-span-2 text-right">Acciones</div>
            </div>

            {{-- Filas --}}
            <div class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                @forelse ($grupos as $grupo)
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 px-4 py-3 items-center transition-colors
                        {{ $loop->even ? 'bg-gray-50 dark:bg-gray-750' : 'bg-white dark:bg-gray-800' }}
                        hover:bg-indigo-50 dark:hover:bg-gray-700">
                        {{-- ID --}}
                        <div class="col-span-1">
                            <span class="md:hidden text-xs font-medium text-gray-500">ID: </span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">#{{ $grupo->id }}</span>
                        </div>

                        {{-- Nombre --}}
                        <div class="col-span-3">
                            <span class="md:hidden text-xs font-medium text-gray-500">Nombre: </span>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $grupo->nombre }}</span>
                        </div>

                        {{-- Departamento --}}
                        <div class="col-span-3">
                            <span class="md:hidden text-xs font-medium text-gray-500">Depto: </span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $grupo->departamento->nombre ?? '-' }}</span>
                        </div>

                        {{-- Encargado --}}
                        <div class="col-span-2">
                            <span class="md:hidden text-xs font-medium text-gray-500">Encargado: </span>
                            <span class="text-sm text-gray-700 dark:text-gray-300 truncate block" title="{{ $grupo->encargado->nombre ?? '-' }}">
                                {{ $grupo->encargado->nombre ?? '-' }}
                            </span>
                        </div>

                        {{-- Miembros --}}
                        <div class="col-span-1">
                            <span class="md:hidden text-xs font-medium text-gray-500">Miembros: </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                {{ $grupo->miembros_count }}
                            </span>
                        </div>

                        {{-- Acciones --}}
                        <div class="col-span-2 flex justify-end items-center space-x-1">
                            <a href="{{ route('grupos.edit', $grupo) }}" wire:navigate
                               class="inline-flex items-center justify-center w-8 h-8 text-secondary hover:bg-secondary-50 dark:hover:bg-secondary-900 rounded-lg transition-colors"
                               title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button wire:click="deleteGrupo({{ $grupo->id }})"
                                onclick="return confirm('¿Esta seguro de eliminar este grupo?')"
                                class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900 rounded-lg transition-colors"
                                title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        No se encontraron grupos registrados.
                    </div>
                @endforelse
            </div>

            <div class="px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                {{ $grupos->links() }}
            </div>
    </div>
</div>
