<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Categoría') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('categorias.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required autofocus />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="descripcion" :value="__('Descripción')" />
                            <textarea id="descripcion" name="descripcion" rows="3" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('descripcion') }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="activo" value="1" checked class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Activo</span>
                            </label>
                        </div>

                        {{-- Etiquetas dinámicas --}}
                        <div class="mb-4">
                            <x-input-label :value="__('Etiquetas (campos personalizados para Observaciones)')" />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                Define etiquetas que aparecerán como campos adicionales al cargar un hecho de esta categoría.
                            </p>
                            <div id="etiquetas-container" class="space-y-3">
                                @php
                                    $etiquetas = old('etiquetas', []);
                                @endphp
                                @forelse($etiquetas as $index => $etiqueta)
                                    @php
                                        $esObjeto = is_array($etiqueta);
                                        $nombre = $esObjeto ? ($etiqueta['nombre'] ?? '') : $etiqueta;
                                        $tipo = $esObjeto ? ($etiqueta['tipo'] ?? 'texto') : 'texto';
                                        $longitud = $esObjeto ? ($etiqueta['longitud'] ?? '') : '';
                                        $opciones = $esObjeto ? ($etiqueta['opciones'] ?? '') : '';
                                        if (is_array($opciones)) $opciones = implode(', ', $opciones);
                                        $requerido = $esObjeto ? ($etiqueta['requerido'] ?? false) : false;
                                    @endphp
                                    <div class="etiqueta-row p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end">
                                            <div class="md:col-span-3">
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre *</label>
                                                <input type="text" name="etiquetas[{{ $index }}][nombre]" value="{{ $nombre }}"
                                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 shadow-sm text-sm"
                                                    placeholder="Ej: Patente" required>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tipo</label>
                                                <select name="etiquetas[{{ $index }}][tipo]" onchange="toggleOpcionesEtiqueta(this)"
                                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 shadow-sm text-sm">
                                                    <option value="texto" {{ $tipo == 'texto' ? 'selected' : '' }}>Texto</option>
                                                    <option value="numero" {{ $tipo == 'numero' ? 'selected' : '' }}>Número</option>
                                                    <option value="fecha" {{ $tipo == 'fecha' ? 'selected' : '' }}>Fecha</option>
                                                    <option value="hora" {{ $tipo == 'hora' ? 'selected' : '' }}>Hora</option>
                                                    <option value="textarea" {{ $tipo == 'textarea' ? 'selected' : '' }}>Texto largo</option>
                                                    <option value="select" {{ $tipo == 'select' ? 'selected' : '' }}>Selección</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2 campo-longitud" style="{{ in_array($tipo, ['texto']) ? '' : 'display:none' }}">
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Longitud máx.</label>
                                                <input type="number" name="etiquetas[{{ $index }}][longitud]" value="{{ $longitud }}"
                                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 shadow-sm text-sm"
                                                    placeholder="Ej: 10" min="1" max="500">
                                            </div>
                                            <div class="md:col-span-3 campo-opciones" style="{{ $tipo == 'select' ? '' : 'display:none' }}">
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Opciones (separadas por coma)</label>
                                                <input type="text" name="etiquetas[{{ $index }}][opciones]" value="{{ $opciones }}"
                                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 shadow-sm text-sm"
                                                    placeholder="Ej: Opción 1, Opción 2">
                                            </div>
                                            <div class="md:col-span-1">
                                                <label class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                    <input type="checkbox" name="etiquetas[{{ $index }}][requerido]" value="1" {{ $requerido ? 'checked' : '' }}
                                                        class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                    Req.
                                                </label>
                                            </div>
                                            <div class="md:col-span-1">
                                                <button type="button" onclick="this.closest('.etiqueta-row').remove()"
                                                    class="w-full px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-md text-xs">
                                                    Quitar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p id="no-etiquetas" class="text-sm text-gray-500 dark:text-gray-400 italic">No hay etiquetas configuradas</p>
                                @endforelse
                            </div>
                            <button type="button" onclick="agregarEtiqueta()"
                                class="mt-3 inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150">
                                + Agregar Etiqueta
                            </button>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('categorias.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Guardar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let etiquetaIndex = {{ count($etiquetas ?? []) }};

        function agregarEtiqueta() {
            const container = document.getElementById('etiquetas-container');
            const noEtiquetas = document.getElementById('no-etiquetas');
            if (noEtiquetas) noEtiquetas.remove();

            const row = document.createElement('div');
            row.className = 'etiqueta-row p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900';
            row.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end">
                    <div class="md:col-span-3">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre *</label>
                        <input type="text" name="etiquetas[${etiquetaIndex}][nombre]" value=""
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 shadow-sm text-sm"
                            placeholder="Ej: Patente" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tipo</label>
                        <select name="etiquetas[${etiquetaIndex}][tipo]" onchange="toggleOpcionesEtiqueta(this)"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 shadow-sm text-sm">
                            <option value="texto">Texto</option>
                            <option value="numero">Número</option>
                            <option value="fecha">Fecha</option>
                            <option value="hora">Hora</option>
                            <option value="textarea">Texto largo</option>
                            <option value="select">Selección</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 campo-longitud">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Longitud máx.</label>
                        <input type="number" name="etiquetas[${etiquetaIndex}][longitud]" value=""
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 shadow-sm text-sm"
                            placeholder="Ej: 10" min="1" max="500">
                    </div>
                    <div class="md:col-span-3 campo-opciones" style="display:none">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Opciones (separadas por coma)</label>
                        <input type="text" name="etiquetas[${etiquetaIndex}][opciones]" value=""
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 shadow-sm text-sm"
                            placeholder="Ej: Opción 1, Opción 2">
                    </div>
                    <div class="md:col-span-1">
                        <label class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                            <input type="checkbox" name="etiquetas[${etiquetaIndex}][requerido]" value="1"
                                class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            Req.
                        </label>
                    </div>
                    <div class="md:col-span-1">
                        <button type="button" onclick="this.closest('.etiqueta-row').remove()"
                            class="w-full px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-md text-xs">
                            Quitar
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(row);
            etiquetaIndex++;
            row.querySelector('input[type="text"]').focus();
        }

        function toggleOpcionesEtiqueta(select) {
            const row = select.closest('.etiqueta-row');
            const campoLongitud = row.querySelector('.campo-longitud');
            const campoOpciones = row.querySelector('.campo-opciones');

            if (select.value === 'texto') {
                campoLongitud.style.display = '';
                campoOpciones.style.display = 'none';
            } else if (select.value === 'select') {
                campoLongitud.style.display = 'none';
                campoOpciones.style.display = '';
            } else {
                campoLongitud.style.display = 'none';
                campoOpciones.style.display = 'none';
            }
        }
    </script>
</x-app-layout>
