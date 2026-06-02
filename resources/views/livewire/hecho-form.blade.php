<div wire:key="hecho-form-{{ $hechoId ?? 'new' }}">
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
            <strong>Errores de validación:</strong>
            <ul class="list-disc list-inside mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Datos Básicos --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Datos del Hecho</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha del Hecho *</label>
                        <input type="date" value="{{ $fecha_hecho }}" wire:model="fecha_hecho" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                        @error('fecha_hecho') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoría *</label>
                        <select wire:model.live="categoria_id" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @selected($categoria->id == $categoria_id)>{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                        @error('categoria_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subcategoría</label>
                        <select wire:model="subcategoria_id" {{ empty($subcategorias) ? 'disabled' : '' }}
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="">Seleccione subcategoría</option>
                            @foreach($subcategorias as $subcategoria)
                                <option value="{{ $subcategoria->id }}" @selected($subcategoria->id == $subcategoria_id)>{{ $subcategoria->nombre }}</option>
                            @endforeach
                        </select>
                        @error('subcategoria_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Horario</label>
                        <select wire:model="horario_id" {{ empty($horarios) ? 'disabled' : '' }}
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="">Seleccione horario</option>
                            @foreach($horarios as $horario)
                                <option value="{{ $horario->id }}" @selected($horario->id == $horario_id)>{{ $horario->nombre }}</option>
                            @endforeach
                        </select>
                        @error('horario_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>
        {{-- Etiquetas dinámicas (si la categoría las tiene) --}}
        @if(!empty($etiquetasCategoria))
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Datos Específicos</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Estos campos son específicos para la categoría seleccionada.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($etiquetasCategoria as $index => $etiqueta)
                        @php
                            // Compatibilidad con formato antiguo (string) y nuevo (objeto)
                            $esObjeto = is_array($etiqueta);
                            $nombre = $esObjeto ? ($etiqueta['nombre'] ?? '') : $etiqueta;
                            $tipo = $esObjeto ? ($etiqueta['tipo'] ?? 'texto') : 'texto';
                            $longitud = $esObjeto ? ($etiqueta['longitud'] ?? null) : null;
                            $opciones = $esObjeto ? ($etiqueta['opciones'] ?? []) : [];
                            $requerido = $esObjeto ? ($etiqueta['requerido'] ?? false) : false;
                            // Crear una clave segura para wire:model (sin espacios ni caracteres especiales)
                            $clave = Str::slug($nombre, '_');
                        @endphp
                        <div class="{{ $tipo === 'textarea' ? 'md:col-span-2' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ $nombre }}
                                @if($requerido) <span class="text-red-500">*</span> @endif
                            </label>

                            @switch($tipo)
                                @case('numero')
                                    <input type="number" wire:model="valoresEtiquetas.{{ $clave }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        placeholder="Ingrese {{ strtolower($nombre) }}"
                                        {{ $requerido ? 'required' : '' }}>
                                    @break

                                @case('fecha')
                                    <input type="date" wire:model="valoresEtiquetas.{{ $clave }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        {{ $requerido ? 'required' : '' }}>
                                    @break

                                @case('hora')
                                    <input type="time" wire:model="valoresEtiquetas.{{ $clave }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        {{ $requerido ? 'required' : '' }}>
                                    @break

                                @case('textarea')
                                    <textarea wire:model="valoresEtiquetas.{{ $clave }}" rows="3"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        placeholder="Ingrese {{ strtolower($nombre) }}"
                                        {{ $requerido ? 'required' : '' }}></textarea>
                                    @break

                                @case('select')
                                    <select wire:model="valoresEtiquetas.{{ $clave }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        {{ $requerido ? 'required' : '' }}>
                                        <option value="">Seleccione...</option>
                                        @foreach($opciones as $opcion)
                                            <option value="{{ $opcion }}">{{ $opcion }}</option>
                                        @endforeach
                                    </select>
                                    @break

                                @default
                                    <input type="text" wire:model="valoresEtiquetas.{{ $clave }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        placeholder="Ingrese {{ strtolower($nombre) }}"
                                        {{ $longitud ? 'maxlength=' . $longitud : '' }}
                                        {{ $requerido ? 'required' : '' }}>
                            @endswitch
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        {{-- Involucrado 1 --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Involucrado 1</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                        <select wire:model="tipo_involucrado1_id" {{ empty($tiposInvolucrados) ? 'disabled' : '' }}
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="">Seleccione tipo</option>
                            @foreach($tiposInvolucrados as $tipo)
                                <option value="{{ $tipo->id }}" @selected($tipo->id == $tipo_involucrado1_id)>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('tipo_involucrado1_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sexo *</label>
                        <select wire:model="sexo_involucrado1" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="Varon" @selected($sexo_involucrado1 == 'Varon')>Varón</option>
                            <option value="Mujer" @selected($sexo_involucrado1 == 'Mujer')>Mujer</option>
                            <option value="Sin datos" @selected($sexo_involucrado1 == 'Sin datos')>Sin datos</option>
                        </select>
                        @error('sexo_involucrado1') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Edad</label>
                        <input type="number" value="{{ $edad_involucrado1 }}" wire:model="edad_involucrado1" min="0" max="120"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                        @error('edad_involucrado1') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Involucrado 2 --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Involucrado 2</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                        <select wire:model="tipo_involucrado2_id" {{ empty($tiposInvolucrados) ? 'disabled' : '' }}
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="">Seleccione tipo</option>
                            @foreach($tiposInvolucrados as $tipo)
                                <option value="{{ $tipo->id }}" @selected($tipo->id == $tipo_involucrado2_id)>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('tipo_involucrado2_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sexo *</label>
                        <select wire:model="sexo_involucrado2" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="Varon" @selected($sexo_involucrado2 == 'Varon')>Varón</option>
                            <option value="Mujer" @selected($sexo_involucrado2 == 'Mujer')>Mujer</option>
                            <option value="Sin datos" @selected($sexo_involucrado2 == 'Sin datos')>Sin datos</option>
                        </select>
                        @error('sexo_involucrado2') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Edad</label>
                        <input type="number" value="{{ $edad_involucrado2 }}" wire:model="edad_involucrado2" min="0" max="120"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                        @error('edad_involucrado2') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Ubicación --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ubicación</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acción</label>
                        <select wire:model="accion_id"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            @disabled(!$categoria_id)>
                            <option value="">Seleccione acción</option>
                            @foreach($acciones as $accion)
                                <option value="{{ $accion->id }}" @selected($accion->id == $accion_id)>{{ $accion->nombre }}</option>
                            @endforeach
                        </select>
                        @error('accion_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desenlace</label>
                        <select wire:model="desenlace_id"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            @disabled(!$categoria_id)>
                            <option value="">Seleccione desenlace</option>
                            @foreach($desenlaces as $desenlace)
                                <option value="{{ $desenlace->id }}" @selected($desenlace->id == $desenlace_id)>{{ $desenlace->nombre }}</option>
                            @endforeach
                        </select>
                        @error('desenlace_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barrio</label>
                        <select wire:model="barrio_id"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="">Seleccione barrio</option>
                            @foreach($barrios as $barrio)
                                <option value="{{ $barrio->id }}" @selected($barrio->id == $barrio_id)>{{ $barrio->nombre }}</option>
                            @endforeach
                        </select>
                        @error('barrio_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-4" wire:ignore>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar Dirección</label>
                    <input type="text" id="searchAddress" placeholder="Ej: Av. Colón 123, Mercedes"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm mb-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        Busca una dirección o haz clic en el mapa para marcar la ubicación
                    </p>
                </div>

                <div class="mb-4" wire:ignore>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ubicación en el Mapa</label>
                    <div id="map" style="height: 400px;" class="rounded-lg border border-gray-300 dark:border-gray-600"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitud</label>
                        <input type="text" wire:model="latitud" id="latitud" readonly
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm bg-gray-100 ">
                        @error('latitud') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitud</label>
                        <input type="text" wire:model="longitud" id="longitud" readonly
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm bg-gray-100">
                        @error('longitud') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>
        

        {{-- Observaciones --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Observaciones</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observaciones adicionales</label>
                    <textarea wire:model="observaciones" rows="4" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" placeholder="Notas adicionales sobre el hecho..."></textarea>
                    @error('observaciones') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end space-x-3">
            <a href="{{ route('hechos.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <span wire:loading.remove>{{ $isEdit ? 'Actualizar' : 'Guardar' }} Hecho</span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        // Usar variables globales para evitar redeclaraciones en navegación Livewire
        if (typeof window.hechoFormMap === 'undefined') {
            window.hechoFormMap = {
                map: null,
                marker: null,
                searchBox: null,
                geocoder: null
            };
        }

        // Función para cargar Google Maps dinámicamente
        window.cargarGoogleMapsForm = function(callback) {
            // Si ya está cargado con todas las librerías necesarias, llamar directamente al callback
            if (typeof google !== 'undefined' &&
                typeof google.maps !== 'undefined' &&
                typeof google.maps.places !== 'undefined') {
                console.log('Google Maps ya está cargado con todas las librerías (Form)');
                callback();
                return;
            }

            // Si ya hay un script cargándose, esperar a que termine
            if (window.googleMapsLoading) {
                console.log('Google Maps se está cargando, esperando... (Form)');
                const interval = setInterval(() => {
                    if (typeof google !== 'undefined' &&
                        typeof google.maps !== 'undefined' &&
                        typeof google.maps.places !== 'undefined') {
                        clearInterval(interval);
                        callback();
                    }
                }, 100);
                return;
            }

            // Marcar que se está cargando
            window.googleMapsLoading = true;

            console.log('Cargando Google Maps con librería places... (Form)');
            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=visualization,places&loading=async';
            script.async = true;
            script.defer = true;
            script.onload = () => {
                console.log('Google Maps cargado exitosamente con places (Form)');
                window.googleMapsLoading = false;
                callback();
            };
            script.onerror = () => {
                console.error('Error al cargar Google Maps (Form)');
                window.googleMapsLoading = false;
            };
            document.head.appendChild(script);
        }

        // Función para inicializar el mapa
        window.inicializarMapaForm = function() {
            console.log('=== Inicializando mapa del formulario ===');

            const defaultLat = {{ $latitud ?? -34.65041121106401 }};
            const defaultLng = {{ $longitud ?? -59.43203992015478 }};

            const mapElement = document.getElementById('map');
            console.log('Elemento mapa:', mapElement);
            console.log('Google Maps disponible:', typeof google !== 'undefined');

            if (!mapElement) {
                console.error('Elemento #map no encontrado en el DOM');
                return;
            }

            if (typeof google === 'undefined') {
                console.error('Google Maps API no está cargada');
                return;
            }

            try {
                // Inicializar mapa
                window.hechoFormMap.map = new google.maps.Map(mapElement, {
                    center: { lat: parseFloat(defaultLat), lng: parseFloat(defaultLng) },
                    zoom: 13,
                    mapTypeControl: true,
                    streetViewControl: true,
                    fullscreenControl: true
                });

                console.log('Mapa inicializado correctamente:', window.hechoFormMap.map);
                // Inicializar geocoder para búsqueda de direcciones
                window.hechoFormMap.geocoder = new google.maps.Geocoder();

                // Si ya hay coordenadas, mostrar marcador
                const latitudActual = {{ $latitud ?? 'null' }};
                const longitudActual = {{ $longitud ?? 'null' }};

                if (latitudActual && longitudActual) {
                    window.placeMarkerForm({ lat: parseFloat(latitudActual), lng: parseFloat(longitudActual) });
                }

                // Configurar búsqueda de direcciones con Autocomplete
                const searchInput = document.getElementById('searchAddress');
                if (searchInput) {
                    const autocomplete = new google.maps.places.Autocomplete(searchInput, {
                        componentRestrictions: { country: 'ar' },
                        fields: ['geometry', 'formatted_address', 'name']
                    });

                    autocomplete.bindTo('bounds', window.hechoFormMap.map);

                    autocomplete.addListener('place_changed', function() {
                        const place = autocomplete.getPlace();

                        if (!place.geometry || !place.geometry.location) {
                            console.log("No se encontró información de ubicación para este lugar");
                            return;
                        }

                        window.hechoFormMap.map.setCenter(place.geometry.location);
                        window.hechoFormMap.map.setZoom(17);

                        window.placeMarkerForm({
                            lat: place.geometry.location.lat(),
                            lng: place.geometry.location.lng()
                        });

                        console.log('Lugar seleccionado:', place.formatted_address || place.name);
                    });
                }

                // Click en el mapa para colocar marcador
                window.hechoFormMap.map.addListener('click', function(event) {
                    window.placeMarkerForm({
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng()
                    });
                });

                console.log('Configuración del mapa completada');
            } catch (error) {
                console.error('Error al inicializar mapa:', error);
            }
        };

        window.placeMarkerForm = function(location) {
            if (window.hechoFormMap.marker) {
                window.hechoFormMap.marker.setPosition(location);
            } else {
                window.hechoFormMap.marker = new google.maps.Marker({
                    position: location,
                    map: window.hechoFormMap.map,
                    draggable: true,
                    animation: google.maps.Animation.DROP
                });

                window.hechoFormMap.marker.addListener('dragend', function(event) {
                    window.updateCoordinatesForm(event.latLng.lat(), event.latLng.lng());
                });
            }

            window.updateCoordinatesForm(location.lat, location.lng);
            window.hechoFormMap.map.panTo(location);
        };

        window.updateCoordinatesForm = function(lat, lng) {
            const latFixed = lat.toFixed(8);
            const lngFixed = lng.toFixed(8);

            @this.set('latitud', latFixed);
            @this.set('longitud', lngFixed);

            // Actualizar inputs visuales
            const latInput = document.getElementById('latitud');
            const lngInput = document.getElementById('longitud');
            if (latInput) latInput.value = latFixed;
            if (lngInput) lngInput.value = lngFixed;
        };

        // Inicializar cuando Livewire navega a esta página
        document.addEventListener('livewire:navigated', function() {
            console.log('Livewire navigated - Formulario de Hechos');

            // Solo ejecutar si estamos en la página con el mapa
            if (!document.getElementById('map')) {
                console.log('No estamos en la página del formulario, saliendo...');
                return;
            }

            // Limpiar instancias anteriores si existen
            if (window.hechoFormMap.marker) {
                window.hechoFormMap.marker.setMap(null);
                window.hechoFormMap.marker = null;
            }
            window.hechoFormMap.map = null;

            // Cargar Google Maps y luego inicializar
            setTimeout(function() {
                window.cargarGoogleMapsForm(function() {
                    console.log('Google Maps listo, inicializando mapa del formulario...');
                    window.inicializarMapaForm();
                });
            }, 100);
        });
    </script>
    @endpush
</div>
