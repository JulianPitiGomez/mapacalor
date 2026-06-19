<div wire:key="operativo-form-{{ $operativoId ?? 'new' }}">
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
        {{-- Datos Basicos del Operativo --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Datos del Operativo</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripcion *</label>
                        <input type="text" wire:model="descripcion" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            placeholder="Ej: Operativo control vehicular zona centro">
                        @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lugar *</label>
                        <input type="text" wire:model="lugar" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            placeholder="Ej: Av. 29 y Calle 22">
                        @error('lugar') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado *</label>
                        <select wire:model="estado" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="planificado">Planificado</option>
                            <option value="en_curso">En Curso</option>
                            <option value="finalizado">Finalizado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                        @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Fecha y Horario --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Fecha y Horario</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha *</label>
                        <input type="date" wire:model="fecha" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                        @error('fecha') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hora Desde *</label>
                        <input type="time" wire:model="hora_desde" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                        @error('hora_desde') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hora Hasta *</label>
                        <input type="time" wire:model="hora_hasta" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                        @error('hora_hasta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Inspector Referente y Participantes --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Inspectores</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inspector Referente *</label>
                        <select wire:model.live="inspector_referente_id" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <option value="">Seleccione el inspector referente</option>
                            @foreach($inspectores as $inspector)
                                <option value="{{ $inspector->id }}">{{ $inspector->nombre }} @if($inspector->dni)(DNI: {{ $inspector->dni }})@endif</option>
                            @endforeach
                        </select>
                        @error('inspector_referente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                        @if(count($operativosActivosDelReferente) > 0)
                        <div class="mt-2 p-3 bg-amber-50 dark:bg-amber-900/30 border border-amber-300 dark:border-amber-700 rounded-md">
                            <p class="text-xs font-semibold text-amber-800 dark:text-amber-300 mb-1">
                                Este inspector ya es referente de {{ count($operativosActivosDelReferente) === 1 ? 'otro operativo activo' : count($operativosActivosDelReferente) . ' operativos activos' }}:
                            </p>
                            <ul class="text-xs text-amber-700 dark:text-amber-400 space-y-0.5">
                                @foreach($operativosActivosDelReferente as $op)
                                <li>
                                    <span class="font-medium">#{{ $op['id'] }}</span>
                                    &mdash; {{ $op['fecha'] }} &mdash; {{ $op['lugar'] }}
                                    <span class="opacity-75">({{ $op['estado'] }})</span>
                                </li>
                                @endforeach
                            </ul>
                            <p class="text-xs text-amber-600 dark:text-amber-500 mt-1">Podés continuar: el sistema admite un inspector referente en varios operativos simultáneos.</p>
                        </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inspectores Participantes</label>
                        <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded-md p-2 bg-white dark:bg-gray-900">
                            @forelse($inspectores as $inspector)
                                <label class="flex items-center space-x-2 py-1 px-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded cursor-pointer">
                                    <input type="checkbox" wire:model="inspectores_ids" value="{{ $inspector->id }}"
                                        class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $inspector->nombre }} @if($inspector->dni)<span class="text-gray-500">({{ $inspector->dni }})</span>@endif
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
        </div>

        {{-- Datos Reales del Operativo (solo en edicion) --}}
        @if($isEdit)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Datos Reales del Operativo</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Estos campos son completados por el encargado del operativo.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hora Apertura Real</label>
                        <input type="time" value="{{ $hora_apertura_real }}" readonly disabled
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm bg-gray-100 dark:bg-gray-950 cursor-not-allowed">
                        @if(!$hora_apertura_real)
                            <p class="text-xs text-gray-400 mt-1">Sin registrar</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hora Cierre Real</label>
                        <input type="time" value="{{ $hora_cierre_real }}" readonly disabled
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm bg-gray-100 dark:bg-gray-950 cursor-not-allowed">
                        @if(!$hora_cierre_real)
                            <p class="text-xs text-gray-400 mt-1">Sin registrar</p>
                        @endif
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acompanamiento Policial</label>
                        <textarea readonly disabled rows="2"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm bg-gray-100 dark:bg-gray-950 cursor-not-allowed">{{ $acompanamiento_policial }}</textarea>
                        @if(!$acompanamiento_policial)
                            <p class="text-xs text-gray-400 mt-1">Sin registrar</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Inspectores Participantes - Estado y Observacion (solo en edicion) --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Asistencia de Inspectores</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">El estado de asistencia y las observaciones son completados por el encargado del operativo.</p>

                @if(count($inspectores_participantes) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Inspector</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">DNI</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Observacion</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($inspectores_participantes as $participante)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $participante['nombre'] }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $participante['dni'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @if($participante['estado'] === 'presente')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Presente
                                        </span>
                                    @elseif($participante['estado'] === 'ausente')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            Ausente
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            Sin registrar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $participante['observacion'] ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay inspectores asignados a este operativo.</p>
                @endif
            </div>
        </div>
        @endif

        {{-- Ubicacion --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ubicacion en el Mapa</h3>

                <div class="mb-4" wire:ignore>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar Direccion</label>
                    <input type="text" id="searchAddressOperativo" placeholder="Ej: Av. 29 y Calle 22, Mercedes"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm mb-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        Busca una direccion o haz clic en el mapa para marcar la ubicacion del operativo
                    </p>
                </div>

                <div class="mb-4" wire:ignore>
                    <div id="mapOperativo" style="height: 400px;" class="rounded-lg border border-gray-300 dark:border-gray-600"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitud</label>
                        <input type="text" wire:model="latitud" id="latitudOperativo" readonly
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm bg-gray-100">
                        @error('latitud') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitud</label>
                        <input type="text" wire:model="longitud" id="longitudOperativo" readonly
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
                    <textarea wire:model="observaciones" rows="4"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                        placeholder="Notas adicionales sobre el operativo..."></textarea>
                    @error('observaciones') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end space-x-3">
            <a href="{{ route('operativos.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <span wire:loading.remove>{{ $isEdit ? 'Actualizar' : 'Guardar' }} Operativo</span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        (function() {
            // Estado del mapa para este formulario
            if (typeof window.operativoFormMap === 'undefined') {
                window.operativoFormMap = {
                    map: null,
                    marker: null,
                    geocoder: null,
                    initialized: false
                };
            }

            // Función global para callback de Google Maps
            window.initGoogleMapsOperativo = function() {
                if (document.getElementById('mapOperativo')) {
                    inicializarMapaOperativo();
                }
            };

            function cargarGoogleMaps(callback) {
                // Si ya está cargado y listo
                if (typeof google !== 'undefined' &&
                    typeof google.maps !== 'undefined' &&
                    typeof google.maps.Map === 'function' &&
                    typeof google.maps.places !== 'undefined') {
                    callback();
                    return;
                }

                // Si hay un script cargándose, esperar
                if (window.googleMapsLoading) {
                    const checkInterval = setInterval(function() {
                        if (typeof google !== 'undefined' &&
                            typeof google.maps !== 'undefined' &&
                            typeof google.maps.Map === 'function' &&
                            typeof google.maps.places !== 'undefined') {
                            clearInterval(checkInterval);
                            callback();
                        }
                    }, 100);
                    return;
                }

                // Verificar si ya existe el script
                const existingScript = document.querySelector('script[src*="maps.googleapis.com"]');
                if (existingScript) {
                    const checkInterval = setInterval(function() {
                        if (typeof google !== 'undefined' &&
                            typeof google.maps !== 'undefined' &&
                            typeof google.maps.Map === 'function' &&
                            typeof google.maps.places !== 'undefined') {
                            clearInterval(checkInterval);
                            callback();
                        }
                    }, 100);
                    return;
                }

                // Cargar el script
                window.googleMapsLoading = true;
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=visualization,places&callback=initGoogleMapsOperativo';
                script.async = true;
                script.defer = true;
                script.onerror = function() {
                    console.error('Error al cargar Google Maps');
                    window.googleMapsLoading = false;
                };
                document.head.appendChild(script);
            }

            function inicializarMapaOperativo() {
                const mapElement = document.getElementById('mapOperativo');

                if (!mapElement) {
                    return;
                }

                if (typeof google === 'undefined' || typeof google.maps === 'undefined' || typeof google.maps.Map !== 'function') {
                    console.log('Google Maps aun no esta listo, esperando...');
                    setTimeout(inicializarMapaOperativo, 200);
                    return;
                }

                // Limpiar instancias anteriores
                if (window.operativoFormMap.marker) {
                    window.operativoFormMap.marker.setMap(null);
                    window.operativoFormMap.marker = null;
                }

                const defaultLat = {{ $latitud ?? -34.65041121106401 }};
                const defaultLng = {{ $longitud ?? -59.43203992015478 }};

                try {
                    window.operativoFormMap.map = new google.maps.Map(mapElement, {
                        center: { lat: parseFloat(defaultLat), lng: parseFloat(defaultLng) },
                        zoom: 13,
                        mapTypeControl: true,
                        streetViewControl: true,
                        fullscreenControl: true
                    });

                    window.operativoFormMap.geocoder = new google.maps.Geocoder();

                    const latitudActual = {{ $latitud ?? 'null' }};
                    const longitudActual = {{ $longitud ?? 'null' }};

                    if (latitudActual && longitudActual) {
                        placeMarkerOperativo({ lat: parseFloat(latitudActual), lng: parseFloat(longitudActual) });
                    }

                    // Configurar autocomplete
                    const searchInput = document.getElementById('searchAddressOperativo');
                    if (searchInput && google.maps.places) {
                        const autocomplete = new google.maps.places.Autocomplete(searchInput, {
                            componentRestrictions: { country: 'ar' },
                            fields: ['geometry', 'formatted_address', 'name']
                        });

                        autocomplete.bindTo('bounds', window.operativoFormMap.map);

                        autocomplete.addListener('place_changed', function() {
                            const place = autocomplete.getPlace();

                            if (!place.geometry || !place.geometry.location) {
                                return;
                            }

                            window.operativoFormMap.map.setCenter(place.geometry.location);
                            window.operativoFormMap.map.setZoom(17);

                            placeMarkerOperativo({
                                lat: place.geometry.location.lat(),
                                lng: place.geometry.location.lng()
                            });
                        });
                    }

                    // Click en el mapa
                    window.operativoFormMap.map.addListener('click', function(event) {
                        placeMarkerOperativo({
                            lat: event.latLng.lat(),
                            lng: event.latLng.lng()
                        });
                    });

                    window.operativoFormMap.initialized = true;
                } catch (error) {
                    console.error('Error al inicializar mapa:', error);
                }
            }

            function placeMarkerOperativo(location) {
                if (!window.operativoFormMap.map) return;

                if (window.operativoFormMap.marker) {
                    window.operativoFormMap.marker.setPosition(location);
                } else {
                    window.operativoFormMap.marker = new google.maps.Marker({
                        position: location,
                        map: window.operativoFormMap.map,
                        draggable: true,
                        animation: google.maps.Animation.DROP
                    });

                    window.operativoFormMap.marker.addListener('dragend', function(event) {
                        updateCoordinatesOperativo(event.latLng.lat(), event.latLng.lng());
                    });
                }

                updateCoordinatesOperativo(location.lat, location.lng);
                window.operativoFormMap.map.panTo(location);
            }

            function updateCoordinatesOperativo(lat, lng) {
                const latFixed = lat.toFixed(8);
                const lngFixed = lng.toFixed(8);

                // Actualizar Livewire
                const component = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                if (component) {
                    component.set('latitud', latFixed);
                    component.set('longitud', lngFixed);
                }

                // Actualizar inputs visuales
                const latInput = document.getElementById('latitudOperativo');
                const lngInput = document.getElementById('longitudOperativo');
                if (latInput) latInput.value = latFixed;
                if (lngInput) lngInput.value = lngFixed;
            }

            // Exponer funciones globalmente
            window.inicializarMapaOperativo = inicializarMapaOperativo;
            window.placeMarkerOperativo = placeMarkerOperativo;
            window.updateCoordinatesOperativo = updateCoordinatesOperativo;

            // Inicializar cuando Livewire navega
            document.addEventListener('livewire:navigated', function() {
                if (!document.getElementById('mapOperativo')) {
                    return;
                }

                window.operativoFormMap.initialized = false;

                setTimeout(function() {
                    cargarGoogleMaps(function() {
                        inicializarMapaOperativo();
                    });
                }, 150);
            });

            // Inicializar en carga inicial si el elemento existe
            if (document.getElementById('mapOperativo')) {
                setTimeout(function() {
                    cargarGoogleMaps(function() {
                        inicializarMapaOperativo();
                    });
                }, 150);
            }
        })();
    </script>
    @endpush
</div>
