<div>
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                <input type="text" wire:model.live="search" placeholder="Descripcion o lugar..."
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

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                <select wire:model.live="filterEstado"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                    <option value="">Todos</option>
                    <option value="planificado">Planificado</option>
                    <option value="en_curso">En Curso</option>
                    <option value="finalizado">Finalizado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Desde</label>
                <input type="date" wire:model.live="filterFechaDesde"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Hasta</label>
                <input type="date" wire:model.live="filterFechaHasta"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button wire:click="toggleMap" type="button"
                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
                {{ $showMap ? 'Ocultar Mapa' : 'Ver en Mapa' }}
            </button>
        </div>
    </div>

    {{-- Datos para el mapa (siempre actualizados por Livewire) --}}
    <div id="operativosMapaData" data-operativos='@json($operativosParaMapa)' style="display:none;"></div>

    {{-- Mapa --}}
    <div x-data="{ showMap: @entangle('showMap') }"
         x-show="showMap"
         x-cloak
         x-init="$watch('showMap', value => { if(value) { setTimeout(() => window.initMapaOperativosList && window.initMapaOperativosList(), 200); } })"
         class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Mapa de Operativos</h3>
        <div wire:ignore>
            <div id="mapOperativosList" style="height: 400px;" class="rounded-lg border border-gray-300 dark:border-gray-600"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            // Estado del mapa para el listado
            if (typeof window.operativosListMap === 'undefined') {
                window.operativosListMap = {
                    map: null,
                    markers: [],
                    initialized: false
                };
            }

            function getOperativosData() {
                const dataElement = document.getElementById('operativosMapaData');
                if (dataElement && dataElement.dataset.operativos) {
                    try {
                        return JSON.parse(dataElement.dataset.operativos);
                    } catch (e) {
                        console.error('Error parsing operativos data:', e);
                        return [];
                    }
                }
                return [];
            }

            function cargarGoogleMaps(callback) {
                if (typeof google !== 'undefined' &&
                    typeof google.maps !== 'undefined' &&
                    typeof google.maps.Map === 'function') {
                    callback();
                    return;
                }

                if (window.googleMapsLoading) {
                    const checkInterval = setInterval(function() {
                        if (typeof google !== 'undefined' &&
                            typeof google.maps !== 'undefined' &&
                            typeof google.maps.Map === 'function') {
                            clearInterval(checkInterval);
                            callback();
                        }
                    }, 100);
                    return;
                }

                const existingScript = document.querySelector('script[src*="maps.googleapis.com"]');
                if (existingScript) {
                    const checkInterval = setInterval(function() {
                        if (typeof google !== 'undefined' &&
                            typeof google.maps !== 'undefined' &&
                            typeof google.maps.Map === 'function') {
                            clearInterval(checkInterval);
                            callback();
                        }
                    }, 100);
                    return;
                }

                window.googleMapsLoading = true;
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=visualization,places&callback=onGoogleMapsLoadedList';
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            }

            window.onGoogleMapsLoadedList = function() {
                window.googleMapsLoading = false;
                inicializarMapa();
            };

            function inicializarMapa() {
                const mapElement = document.getElementById('mapOperativosList');

                if (!mapElement) {
                    return;
                }

                if (typeof google === 'undefined' || typeof google.maps === 'undefined' || typeof google.maps.Map !== 'function') {
                    setTimeout(inicializarMapa, 200);
                    return;
                }

                // Limpiar markers anteriores
                if (window.operativosListMap.markers) {
                    window.operativosListMap.markers.forEach(function(marker) {
                        marker.setMap(null);
                    });
                    window.operativosListMap.markers = [];
                }

                try {
                    window.operativosListMap.map = new google.maps.Map(mapElement, {
                        center: { lat: -34.65041121106401, lng: -59.43203992015478 },
                        zoom: 12
                    });

                    const bounds = new google.maps.LatLngBounds();
                    const infoWindow = new google.maps.InfoWindow();

                    const estadoColors = {
                        'planificado': '#3B82F6',
                        'en_curso': '#F59E0B',
                        'finalizado': '#10B981',
                        'cancelado': '#EF4444'
                    };

                    // Leer datos actualizados desde el elemento HTML
                    const operativosData = getOperativosData();

                    if (operativosData.length === 0) {
                        console.log('No hay operativos con coordenadas para mostrar en el mapa');
                        return;
                    }

                    operativosData.forEach(function(op) {
                        const position = { lat: parseFloat(op.latitud), lng: parseFloat(op.longitud) };

                        const marker = new google.maps.Marker({
                            position: position,
                            map: window.operativosListMap.map,
                            title: op.descripcion,
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                fillColor: estadoColors[op.estado] || '#6B7280',
                                fillOpacity: 1,
                                strokeWeight: 2,
                                strokeColor: '#ffffff',
                                scale: 10
                            }
                        });

                        window.operativosListMap.markers.push(marker);
                        bounds.extend(position);

                        const content = `
                            <div style="padding: 10px; min-width: 250px;">
                                <h4 style="font-weight: bold; color: #1f2937; margin-bottom: 8px; font-size: 14px;">${op.descripcion}</h4>
                                <p style="font-size: 12px; color: #4b5563; margin: 4px 0;"><strong>Lugar:</strong> ${op.lugar}</p>
                                <p style="font-size: 12px; color: #4b5563; margin: 4px 0;"><strong>Fecha:</strong> ${op.fecha}</p>
                                <p style="font-size: 12px; color: #4b5563; margin: 4px 0;"><strong>Horario:</strong> ${op.hora_desde} - ${op.hora_hasta}</p>
                                <p style="font-size: 12px; color: #4b5563; margin: 4px 0;"><strong>Departamento:</strong> ${op.departamento}</p>
                                <p style="font-size: 12px; color: #4b5563; margin: 4px 0;"><strong>Referente:</strong> ${op.inspector_referente}</p>
                                <p style="font-size: 12px; margin: 8px 0;">
                                    <span style="padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; background-color: ${estadoColors[op.estado]}20; color: ${estadoColors[op.estado]}">${op.estado_label}</span>
                                </p>
                                <a href="/operativos/${op.id}/edit" style="display: inline-block; margin-top: 8px; padding: 6px 12px; background-color: #4f46e5; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">Ver / Editar Operativo</a>
                            </div>
                        `;

                        marker.addListener('click', function() {
                            infoWindow.setContent(content);
                            infoWindow.open(window.operativosListMap.map, marker);
                        });
                    });

                    if (operativosData.length > 0) {
                        window.operativosListMap.map.fitBounds(bounds);
                    }

                    window.operativosListMap.initialized = true;
                } catch (error) {
                    console.error('Error al inicializar mapa del listado:', error);
                }
            }

            // Función global que llama Alpine cuando showMap cambia a true
            window.initMapaOperativosList = function() {
                cargarGoogleMaps(function() {
                    inicializarMapa();
                });
            };

            // Inicializar cuando Livewire navega
            document.addEventListener('livewire:navigated', function() {
                window.operativosListMap.initialized = false;
            });
        })();
    </script>
    @endpush

    {{-- Listado de Operativos --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg" style="overflow: visible;">
        <div class="p-4" style="overflow: visible;">
            {{-- Encabezado --}}
            <div class="hidden md:grid md:grid-cols-12 gap-2 px-4 py-2 bg-primary dark:bg-primary-700 rounded-t-lg text-xs font-medium text-white uppercase tracking-wider">
                <div class="col-span-1">ID</div>
                <div class="col-span-2">Fecha / Horario</div>
                <div class="col-span-3">Descripcion / Lugar</div>
                <div class="col-span-2">Departamento</div>
                <div class="col-span-2">Referente</div>
                <div class="col-span-1">Estado</div>
                <div class="col-span-1 text-right">Acciones</div>
            </div>

            {{-- Filas --}}
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($operativos as $operativo)
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-750 items-center">
                        {{-- ID --}}
                        <div class="col-span-1">
                            <span class="md:hidden text-xs font-medium text-gray-500">ID: </span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">#{{ $operativo->id }}</span>
                        </div>

                        {{-- Fecha y Horario --}}
                        <div class="col-span-2">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $operativo->fecha->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($operativo->hora_desde)->format('H:i') }} - {{ \Carbon\Carbon::parse($operativo->hora_hasta)->format('H:i') }}
                            </div>
                        </div>

                        {{-- Descripcion y Lugar --}}
                        <div class="col-span-3">
                            <div class="text-sm text-gray-900 dark:text-gray-100 truncate" title="{{ $operativo->descripcion }}">
                                {{ $operativo->descripcion }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate" title="{{ $operativo->lugar }}">
                                {{ $operativo->lugar }}
                            </div>
                        </div>

                        {{-- Departamento --}}
                        <div class="col-span-2">
                            <span class="md:hidden text-xs font-medium text-gray-500">Depto: </span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $operativo->departamento->nombre ?? '-' }}</span>
                        </div>

                        {{-- Referente --}}
                        <div class="col-span-2">
                            <span class="md:hidden text-xs font-medium text-gray-500">Ref: </span>
                            <span class="text-sm text-gray-700 dark:text-gray-300 truncate block" title="{{ $operativo->inspectorReferente->nombre ?? '-' }}">
                                {{ $operativo->inspectorReferente->nombre ?? '-' }}
                            </span>
                        </div>

                        {{-- Estado --}}
                        <div class="col-span-1">
                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full {{ $operativo->estado_badge_class }}">
                                {{ $operativo->estado_label }}
                            </span>
                        </div>

                        {{-- Acciones --}}
                        <div class="col-span-1 flex justify-end items-center space-x-1">
                            {{-- Menu de cambio de estado --}}
                            <div x-data="{
                                open: false,
                                dropdownPosition: { top: 0, left: 0 },
                                updatePosition() {
                                    const btn = this.$refs.dropdownBtn;
                                    const rect = btn.getBoundingClientRect();
                                    this.dropdownPosition = {
                                        top: rect.top + window.scrollY - 130,
                                        left: rect.left + window.scrollX - 100
                                    };
                                }
                            }">
                                <button x-ref="dropdownBtn" @click="updatePosition(); open = !open" type="button"
                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                    title="Cambiar estado">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                                <div x-show="open"
                                    @click.away="open = false"
                                    x-transition
                                    :style="'position: fixed; top: ' + dropdownPosition.top + 'px; left: ' + dropdownPosition.left + 'px;'"
                                    class="w-36 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-[9999]">
                                    <div class="py-1">
                                        @foreach(['planificado' => 'Planificado', 'en_curso' => 'En Curso', 'finalizado' => 'Finalizado', 'cancelado' => 'Cancelado'] as $estado => $label)
                                            @if($operativo->estado !== $estado)
                                                <button wire:click="cambiarEstado({{ $operativo->id }}, '{{ $estado }}')" @click="open = false"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    {{ $label }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('operativos.edit', $operativo) }}" wire:navigate
                               class="inline-flex items-center justify-center w-8 h-8 text-secondary hover:bg-secondary-50 dark:hover:bg-secondary-900 rounded-lg transition-colors"
                               title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button wire:click="deleteOperativo({{ $operativo->id }})"
                                onclick="return confirm('¿Esta seguro de eliminar este operativo?')"
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
                        No se encontraron operativos registrados.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $operativos->links() }}
            </div>
        </div>
    </div>
</div>
