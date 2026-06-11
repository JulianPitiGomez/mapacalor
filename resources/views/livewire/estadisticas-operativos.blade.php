<div>
    {{-- ================================================================ --}}
    {{-- FILTROS                                                           --}}
    {{-- ================================================================ --}}
    <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Filtros</h3>
            <button wire:click="toggleMapa" type="button"
                class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/>
                </svg>
                {{ $showMapa ? 'Ocultar Mapa de Calor' : 'Generar Mapa de Calor' }}
                @if(!$showMapa && ($totalActasMapa + $totalRegistrosMapa) > 0)
                    <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">
                        {{ $totalActasMapa + $totalRegistrosMapa }}
                    </span>
                @endif
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Período</label>
                <select wire:model.live="filterQuick"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                    <option value="">— Personalizado —</option>
                    <option value="hoy">Hoy</option>
                    <option value="semana">Esta semana</option>
                    <option value="mes">Este mes</option>
                    <option value="todos">Sin filtro de fecha</option>
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
    </div>

    {{-- ================================================================ --}}
    {{-- MAPA DE CALOR                                                     --}}
    {{-- ================================================================ --}}

    {{-- Datos para el mapa — usar json_encode + {{ }} para que el browser decodifique &quot; → " antes de JSON.parse --}}
    <div id="mapaCalorData" data-puntos="{{ json_encode($mapaPuntos->toArray()) }}" style="display:none;"></div>

    <div x-data="{ showMapa: @entangle('showMapa') }"
         x-show="showMapa"
         x-cloak
         x-init="$watch('showMapa', value => { if(value) { setTimeout(() => window.initMapaCalor && window.initMapaCalor(), 200); } })"
         class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">

        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Mapa de Calor — Operativos</h3>
            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 inline-block"></span>
                    {{ $mapaPuntos->count() }} operativos
                </span>
                <span class="inline-flex items-center gap-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                    {{ $totalActasMapa }} actas
                </span>
                <span class="inline-flex items-center gap-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
                    {{ $totalRegistrosMapa }} registros
                </span>
                <span class="text-gray-400 dark:text-gray-600 hidden md:inline">· Clic en un punto para ver detalle</span>
            </div>
        </div>

        <div wire:ignore>
            <div id="mapaCalorCanvas" style="height: 450px;" class="rounded-lg border border-gray-300 dark:border-gray-600"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            window.mapaCalorState = window.mapaCalorState || { map: null, heatmapCircles: [], markers: [], infoWindow: null };

            // Lee los puntos del div de datos
            function getPuntosData() {
                var el = document.getElementById('mapaCalorData');
                if (!el) return [];
                var raw = el.dataset.puntos;
                if (!raw) return [];
                try { return JSON.parse(raw); }
                catch (e) { console.error('[HeatMap] JSON.parse error:', e, raw.substring(0, 80)); return []; }
            }

            // Dibuja el "mapa de calor" con círculos translúcidos (HeatmapLayer está deprecado
            // y ya no renderiza). Mismo enfoque que /estadisticas. Solo requiere el core de Maps.
            function inicializarHeatmap() {
                var mapElement = document.getElementById('mapaCalorCanvas');
                if (!mapElement) return;

                // Maps core no listo todavía
                if (!window.google || !google.maps || typeof google.maps.Map !== 'function') {
                    setTimeout(inicializarHeatmap, 200);
                    return;
                }

                // Limpiar capa anterior si existe
                if (window.mapaCalorState.heatmapCircles && window.mapaCalorState.heatmapCircles.length) {
                    window.mapaCalorState.heatmapCircles.forEach(function(c) { c.setMap(null); });
                    window.mapaCalorState.heatmapCircles = [];
                }
                if (window.mapaCalorState.markers && window.mapaCalorState.markers.length) {
                    window.mapaCalorState.markers.forEach(function(m) { m.setMap(null); });
                    window.mapaCalorState.markers = [];
                }
                if (window.mapaCalorState.infoWindow) {
                    window.mapaCalorState.infoWindow.close();
                }

                // Crear mapa
                var map = new google.maps.Map(mapElement, {
                    center: { lat: -34.65041121106401, lng: -59.43203992015478 },
                    zoom: 12
                });
                window.mapaCalorState.map = map;

                var puntos = getPuntosData();
                console.log('[HeatMap] puntos cargados:', puntos.length);

                if (puntos.length === 0) return;

                var bounds   = new google.maps.LatLngBounds();
                var infoWin  = new google.maps.InfoWindow();
                window.mapaCalorState.infoWindow = infoWin;
                window.mapaCalorState.heatmapCircles = [];

                var estadoColors = { planificado:'#3B82F6', en_curso:'#F59E0B', finalizado:'#10B981', cancelado:'#EF4444' };

                window.mapaCalorState.markers = puntos.map(function(p) {
                    var pos = { lat: parseFloat(p.lat), lng: parseFloat(p.lng) };
                    bounds.extend(pos);

                    // Círculo de "calor": radio y opacidad según (actas + registros)
                    var peso  = (p.total_actas || 0) + (p.total_registros || 0);
                    var radio = 180 + Math.min(peso, 30) * 25; // 180 m base, crece con la actividad
                    var circle = new google.maps.Circle({
                        strokeColor: '#FF3B30', strokeOpacity: 0.35, strokeWeight: 1,
                        fillColor: '#FF3B30', fillOpacity: 0.35,
                        map: map, center: pos, radius: radio, clickable: true
                    });
                    window.mapaCalorState.heatmapCircles.push(circle);

                    var color = estadoColors[p.estado] || '#9CA3AF';
                    var html = '<div style="padding:10px;min-width:260px;font-family:system-ui,sans-serif;">' +
                        '<div style="font-weight:700;font-size:14px;color:#1f2937;margin-bottom:6px;line-height:1.3;">' + (p.descripcion||'') + '</div>' +
                        '<p style="font-size:12px;color:#4b5563;margin:3px 0;"><strong>Lugar:</strong> ' + (p.lugar||'') + '</p>' +
                        '<p style="font-size:12px;color:#4b5563;margin:3px 0;"><strong>Fecha:</strong> ' + (p.fecha||'') + '</p>' +
                        '<p style="font-size:12px;color:#4b5563;margin:3px 0;"><strong>Departamento:</strong> ' + (p.departamento||'') + '</p>' +
                        '<p style="font-size:12px;margin:8px 0 10px;"><span style="padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600;background-color:' + color + '20;color:' + color + ';">' + (p.estado||'') + '</span></p>' +
                        '<div style="display:flex;gap:6px;flex-wrap:wrap;">' +
                        '<span style="background:#dbeafe;color:#1d4ed8;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;">' + (p.total_actas||0) + ' actas</span>' +
                        '<span style="background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;">' + (p.total_registros||0) + ' registros</span>' +
                        '<span style="background:#f3f4f6;color:#374151;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">' + peso + ' total</span>' +
                        '</div></div>';

                    // Marcador invisible centrado para anclar el popup de detalle
                    var marker = new google.maps.Marker({
                        position: pos, map: map,
                        icon: { path: google.maps.SymbolPath.CIRCLE, scale: 14,
                                fillColor:'#fff', fillOpacity:0.01, strokeColor:'#fff', strokeOpacity:0.01, strokeWeight:1 },
                        title: p.descripcion || ''
                    });
                    function abrir() { infoWin.setContent(html); infoWin.open(map, marker); }
                    marker.addListener('click', abrir);
                    circle.addListener('click', abrir);
                    return marker;
                });

                map.fitBounds(bounds);
            }

            // Punto de entrada: carga el core de Maps si no está, luego dibuja
            window.initMapaCalor = function() {
                // Maps ya listo → dibuja de inmediato
                if (window.google && google.maps && typeof google.maps.Map === 'function') {
                    inicializarHeatmap();
                    return;
                }

                // Maps no cargado aún — agregar script solo si no existe ya
                if (!document.querySelector('script[src*="maps.googleapis.com"]')) {
                    window.onGoogleMapsLoadedCalor = function() { inicializarHeatmap(); };
                    var s = document.createElement('script');
                    s.src = 'https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=onGoogleMapsLoadedCalor';
                    s.async = true; s.defer = true;
                    document.head.appendChild(s);
                } else {
                    // Script Maps existe pero todavía cargando — esperar al core
                    var t = setInterval(function() {
                        if (window.google && google.maps && typeof google.maps.Map === 'function') {
                            clearInterval(t);
                            inicializarHeatmap();
                        }
                    }, 150);
                }
            };

            // Auto-refrescar datos cuando Livewire re-renderiza con el mapa abierto
            if (!window._mapaCalorHookRegistered) {
                window._mapaCalorHookRegistered = true;
                Livewire.hook('commit', ({ component, succeed }) => {
                    succeed(() => {
                        if (component.name !== 'estadisticas-operativos') return;
                        var canvas = document.getElementById('mapaCalorCanvas');
                        if (!canvas || canvas.offsetParent === null) return;
                        setTimeout(function() { window.initMapaCalor && window.initMapaCalor(); }, 150);
                    });
                });
            }

            document.addEventListener('livewire:navigated', function() {
                window.mapaCalorState = { map: null, heatmapCircles: [], markers: [], infoWindow: null };
                window._mapaCalorHookRegistered = false;
            });
        })();
    </script>
    @endpush

    {{-- ================================================================ --}}
    {{-- ESTADÍSTICAS DEL PERÍODO                                          --}}
    {{-- ================================================================ --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Estadísticas del período
        </h3>
        <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
    </div>

    {{-- Tarjetas resumen --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">

        {{-- Operativos --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operativos</span>
                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalOperativos }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">en el período filtrado</div>
        </div>

        {{-- Actas --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actas</span>
                <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $statsActas->total_actas ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">labradas en operativos</div>
        </div>

        {{-- Registros --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Registros</span>
                <div class="w-8 h-8 bg-green-50 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalRegistros }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">revisiones sin infracción</div>
        </div>

        {{-- Secuestros --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Secuestros</span>
                <div class="w-8 h-8 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $statsActas->total_secuestros ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">vehículos secuestrados</div>
        </div>

        {{-- Decomisos --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Decomisos</span>
                <div class="w-8 h-8 bg-red-50 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $statsActas->total_decomisos ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                + {{ $statsActas->total_clausuras ?? 0 }} clausuras
                · {{ $statsActas->total_retiene_lic ?? 0 }} lic. retenidas
            </div>
        </div>

    </div>

    {{-- ================================================================ --}}
    {{-- ACTAS POR OPERATIVO                                               --}}
    {{-- ================================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Actas por operativo</h4>
            <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">Clic en un operativo para ver sus actas</span>
        </div>

        @if($actasPorOperativo->isEmpty())
            <div class="px-4 py-10 text-center text-gray-400 dark:text-gray-500 text-sm">
                <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ningún operativo del período tiene actas asociadas en el sistema de faltas
            </div>
        @else
            <div class="hidden md:grid grid-cols-12 px-4 py-2 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                <div class="col-span-5">Operativo</div>
                <div class="col-span-1 text-center">Fecha</div>
                <div class="col-span-1 text-center">Actas</div>
                <div class="col-span-1 text-center">Secu.</div>
                <div class="col-span-1 text-center">Dec.</div>
                <div class="col-span-1 text-center">Clau.</div>
                <div class="col-span-1 text-center">Lic.</div>
                <div class="col-span-1"></div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($actasPorOperativo as $opFila)
                @php $actas = $actasDetalle->get($opFila->operativo_id, collect()); @endphp
                <div x-data="{ open: false }">

                    <div @click="open = !open"
                         class="grid grid-cols-12 px-4 py-3 cursor-pointer items-center transition-colors"
                         :class="open ? 'bg-indigo-50 dark:bg-indigo-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30'">

                        <div class="col-span-5">
                            <div class="font-medium text-sm text-gray-900 dark:text-gray-100 truncate" title="{{ $opFila->descripcion }}">
                                {{ $opFila->descripcion }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                #{{ $opFila->operativo_id }} · {{ $opFila->departamento_nombre }} · {{ $opFila->lugar }}
                            </div>
                        </div>

                        <div class="col-span-1 text-center text-xs text-gray-600 dark:text-gray-300">
                            {{ \Carbon\Carbon::parse($opFila->fecha)->format('d/m/Y') }}
                        </div>

                        <div class="col-span-1 text-center">
                            <span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                {{ $opFila->total_actas }}
                            </span>
                        </div>

                        <div class="col-span-1 text-center">
                            @if($opFila->secuestros > 0)
                                <span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300">{{ $opFila->secuestros }}</span>
                            @else
                                <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                            @endif
                        </div>

                        <div class="col-span-1 text-center">
                            @if($opFila->decomisos > 0)
                                <span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">{{ $opFila->decomisos }}</span>
                            @else
                                <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                            @endif
                        </div>

                        <div class="col-span-1 text-center">
                            @if($opFila->clausuras > 0)
                                <span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300">{{ $opFila->clausuras }}</span>
                            @else
                                <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                            @endif
                        </div>

                        <div class="col-span-1 text-center">
                            @if($opFila->retiene_lic > 0)
                                <span class="inline-flex items-center justify-center min-w-[26px] px-1.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">{{ $opFila->retiene_lic }}</span>
                            @else
                                <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                            @endif
                        </div>

                        <div class="col-span-1 flex justify-end pr-1">
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                 :class="open ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="border-t border-indigo-100 dark:border-indigo-900/30 bg-slate-50 dark:bg-gray-900/40">

                        @if($actas->isEmpty())
                            <div class="px-8 py-4 text-sm text-gray-400 dark:text-gray-500 italic">
                                No se encontraron actas individuales para este operativo.
                            </div>
                        @else
                            <div class="hidden md:grid grid-cols-12 px-6 py-1.5 bg-slate-100 dark:bg-gray-800/60 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider border-b border-slate-200 dark:border-gray-700">
                                <div class="col-span-1">Acta</div>
                                <div class="col-span-2">Fecha / Hora</div>
                                <div class="col-span-3">Infractor</div>
                                <div class="col-span-2">Vehículo</div>
                                <div class="col-span-2">Medidas</div>
                                <div class="col-span-1">Inspector</div>
                                <div class="col-span-1"></div>
                            </div>

                            <div class="divide-y divide-slate-100 dark:divide-gray-700/50">
                            @foreach($actas as $acta)
                            <div x-data="{ detail: false }">

                                <div @click="detail = !detail"
                                     class="grid grid-cols-12 px-6 py-2.5 cursor-pointer items-center transition-colors text-xs"
                                     :class="detail ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-white dark:hover:bg-gray-800/40'">

                                    <div class="col-span-1">
                                        <span class="font-mono font-bold text-blue-700 dark:text-blue-400">#{{ $acta->actanro }}</span>
                                    </div>

                                    <div class="col-span-2">
                                        <div class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</div>
                                        <div class="text-gray-400">{{ substr($acta->hora ?? '', 0, 5) }}</div>
                                    </div>

                                    <div class="col-span-3">
                                        <div class="font-medium text-gray-800 dark:text-gray-200 truncate" title="{{ $acta->nombreinf }}">
                                            {{ $acta->nombreinf ?? '(sin datos)' }}
                                        </div>
                                        @if($acta->dni)
                                            <div class="text-gray-400">DNI {{ number_format($acta->dni, 0, ',', '.') }}</div>
                                        @endif
                                        @if($acta->motivos)
                                            <div class="text-indigo-500 dark:text-indigo-400 truncate mt-0.5" title="{{ $acta->motivos }}">
                                                {{ $acta->motivos }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-span-2">
                                        <div class="font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $acta->dominio ?? '—' }}</div>
                                        <div class="text-gray-400 truncate">{{ trim(($acta->marca_vehiculo ?? '') . ' ' . ($acta->tipo_rodado ?? '')) ?: '—' }}</div>
                                    </div>

                                    <div class="col-span-2 flex flex-wrap gap-1">
                                        @if($acta->secuestro) <span class="px-1.5 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">Secu.</span> @endif
                                        @if($acta->decomiso) <span class="px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">Dec.</span> @endif
                                        @if($acta->clausura) <span class="px-1.5 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">Clau.</span> @endif
                                        @if($acta->retiene_lic) <span class="px-1.5 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">Lic.</span> @endif
                                        @if(!$acta->secuestro && !$acta->decomiso && !$acta->clausura && !$acta->retiene_lic)
                                            <span class="text-gray-300 dark:text-gray-600">—</span>
                                        @endif
                                    </div>

                                    <div class="col-span-1 text-gray-500 dark:text-gray-400 truncate" title="{{ $acta->inspector_nombre }}">
                                        {{ $acta->inspector_nombre ?? '—' }}
                                    </div>

                                    <div class="col-span-1 flex justify-end pr-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                                             :class="detail ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>

                                <div x-show="detail"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     class="px-6 py-4 bg-blue-50 dark:bg-blue-900/10 border-t border-blue-100 dark:border-blue-900/30">

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">

                                        @if($acta->motivos)
                                        <div class="md:col-span-3 bg-white dark:bg-gray-800 rounded-lg p-3 border border-indigo-100 dark:border-indigo-900/40">
                                            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Motivos de Infracción</div>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach(explode(' | ', $acta->motivos) as $motivo)
                                                    <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 text-xs font-medium">{{ trim($motivo) }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-100 dark:border-gray-700">
                                            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Infractor</div>
                                            <div class="space-y-1 text-gray-700 dark:text-gray-300">
                                                <div><span class="text-gray-400">Nombre:</span> {{ $acta->nombreinf ?? '—' }}</div>
                                                <div><span class="text-gray-400">DNI:</span> {{ $acta->dni ? number_format($acta->dni, 0, ',', '.') : '—' }}</div>
                                                <div><span class="text-gray-400">Domicilio:</span> {{ $acta->direcinf ?? '—' }}</div>
                                                @if($acta->licencia)
                                                    <div><span class="text-gray-400">N° Licencia:</span> {{ $acta->licencia }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-100 dark:border-gray-700">
                                            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Vehículo</div>
                                            <div class="space-y-1 text-gray-700 dark:text-gray-300">
                                                <div><span class="text-gray-400">Dominio:</span> <span class="font-mono font-bold">{{ $acta->dominio ?? '—' }}</span></div>
                                                <div><span class="text-gray-400">Marca:</span> {{ $acta->marca_vehiculo ?? '—' }}</div>
                                                <div><span class="text-gray-400">Tipo:</span> {{ $acta->tipo_rodado ?? '—' }}</div>
                                                <div><span class="text-gray-400">Modelo:</span> {{ $acta->modelo ?? '—' }}</div>
                                                @if($acta->grad_alcohol > 0)
                                                    <div class="text-red-600 dark:text-red-400 font-semibold">
                                                        <span class="text-gray-400 font-normal">Alcohol:</span> {{ $acta->grad_alcohol }}‰
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-100 dark:border-gray-700">
                                            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Medidas e Inspector</div>
                                            <div class="space-y-1 text-gray-700 dark:text-gray-300">
                                                <div class="flex flex-wrap gap-1 mb-2">
                                                    @if($acta->secuestro) <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Secuestro</span> @endif
                                                    @if($acta->decomiso) <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Decomiso</span> @endif
                                                    @if($acta->clausura) <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Clausura</span> @endif
                                                    @if($acta->retiene_lic) <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Ret. Licencia</span> @endif
                                                </div>
                                                <div><span class="text-gray-400">Inspector:</span> {{ $acta->inspector_nombre ?? '—' }}</div>
                                                @if($acta->obs)
                                                    <div class="mt-1 pt-1 border-t border-gray-100 dark:border-gray-700">
                                                        <span class="text-gray-400">Obs:</span> {{ $acta->obs }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
            </div>
        @endif

    </div>

</div>
