<div>
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
            <strong>Errores de validacion:</strong>
            <ul class="list-disc list-inside mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Datos del Grupo --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Datos del Grupo</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre del Grupo *</label>
                        <input type="text" wire:model="nombre" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            placeholder="Ej: Grupo 1 - Zona Centro">
                        @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Departamento *</label>
                        <select wire:model.live="departamento_id" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="">Seleccione un departamento</option>
                            @foreach($departamentos as $departamento)
                                <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                            @endforeach
                        </select>
                        @error('departamento_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Encargado del Grupo *</label>
                        <select wire:model.live="inspector_encargado_id" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="">Seleccione el encargado</option>
                            @foreach($inspectores as $inspector)
                                <option value="{{ $inspector->id }}">{{ $inspector->nombre }} @if($inspector->dni)(DNI: {{ $inspector->dni }})@endif</option>
                            @endforeach
                        </select>
                        @error('inspector_encargado_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Miembros del Grupo --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Miembros del Grupo</h3>

                <div>
                    <div class="max-h-64 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded-md p-2 bg-white dark:bg-gray-900">
                        @forelse($inspectores as $inspector)
                            <label class="flex items-center space-x-2 py-1 px-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded cursor-pointer">
                                <input type="checkbox" wire:model="inspectores_ids" value="{{ $inspector->id }}"
                                    class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $inspector->nombre }} @if($inspector->dni)<span class="text-gray-500">({{ $inspector->dni }})</span>@endif
                                    @if($inspector->id == $inspector_encargado_id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 ml-1">Encargado</span>
                                    @endif
                                </span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 py-2">
                                @if($departamento_id)
                                    No hay inspectores en este departamento.
                                @else
                                    Seleccione un departamento primero.
                                @endif
                            </p>
                        @endforelse
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Seleccionados: {{ count($inspectores_ids) }}
                    </p>
                    @error('inspectores_ids') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end space-x-3">
            <a href="{{ route('grupos.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <span wire:loading.remove>{{ $isEdit ? 'Actualizar' : 'Guardar' }} Grupo</span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>
    </form>
</div>
