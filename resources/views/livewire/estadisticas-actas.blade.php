@php
    $inputClass = 'w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm';
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1';
    $cardClass = 'bg-white dark:bg-gray-800 p-4 rounded-lg shadow';
    $total = (int) ($totales->total ?? 0);
    $totalCamaras = (int) ($totales->camaras ?? 0);
    $totalOtros = (int) ($totales->otros ?? 0);
    $totalManuales = $total - $totalCamaras - $totalOtros;
    $porcentaje = fn ($n) => $total > 0 ? round($n * 100 / $total, 1) : 0;
    // Filtros que viven en la fila desplegable: si alguno está puesto, arranca abierta
    // y el botón muestra cuántos hay, para que no queden filtros escondidos sin aviso.
    $filtrosAvanzados = collect([$filterInspector, $filterDepartamento, $filterEstado, trim($buscar)])
        ->filter(fn ($v) => $v !== '')
        ->count();
    $medidasTotal = (int) ($medidas->total ?? 0);
    $medidasConMedida = (int) ($medidas->con_medida ?? 0);
@endphp

<div>
    {{-- ================================================================ --}}
    {{-- FILTROS                                                           --}}
    {{-- ================================================================ --}}
    <div class="mb-6 {{ $cardClass }}" x-data="{ masFiltros: {{ $filtrosAvanzados ? 'true' : 'false' }} }">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Filtros</h3>
            <div class="flex items-center gap-3">
                <span wire:loading class="text-xs text-gray-400">Cargando…</span>
                <button type="button" @click="masFiltros = !masFiltros"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <span x-text="masFiltros ? 'Menos filtros' : 'Más filtros'">Más filtros</span>
                    @if($filtrosAvanzados)
                        <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 text-[0.65rem] font-bold">{{ $filtrosAvanzados }}</span>
                    @endif
                    <svg class="w-3.5 h-3.5 transition-transform" :class="masFiltros ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <button wire:click="limpiarFiltros" type="button"
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Limpiar filtros
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="{{ $labelClass }}">Período</label>
                <select wire:model.live="filterQuick" class="{{ $inputClass }}">
                    <option value="">— Personalizado —</option>
                    <option value="hoy">Hoy</option>
                    <option value="semana">Esta semana</option>
                    <option value="mes">Este mes</option>
                    <option value="anio">Este año</option>
                    <option value="12meses">Últimos 12 meses</option>
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Fecha Desde</label>
                <input type="date" wire:model.live="filterFechaDesde" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Fecha Hasta</label>
                <input type="date" wire:model.live="filterFechaHasta" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Origen</label>
                <select wire:model.live="filterOrigen" class="{{ $inputClass }}">
                    <option value="">Todas</option>
                    <option value="camaras">Cámaras (con preacta)</option>
                    <option value="manuales">Manuales del sistema</option>
                    @if($hayOtros)
                        <option value="otros">Otros</option>
                    @endif
                </select>
            </div>
        </div>

        {{-- Segunda fila: aparece solo al desplegar "Más filtros" --}}
        <div x-show="masFiltros" x-cloak x-transition.opacity
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <div>
                <label class="{{ $labelClass }}">Inspector</label>
                <select wire:model.live="filterInspector" class="{{ $inputClass }}">
                    <option value="">Todos</option>
                    @foreach($inspectores as $inspector)
                        <option value="{{ $inspector->id }}">{{ $inspector->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Departamento</label>
                <select wire:model.live="filterDepartamento" class="{{ $inputClass }}">
                    <option value="">Todos</option>
                    @foreach($departamentos as $departamento)
                        <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Estado</label>
                <select wire:model.live="filterEstado" class="{{ $inputClass }}">
                    <option value="">Todos</option>
                    @foreach($estados as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Buscar</label>
                <input type="text" wire:model.live.debounce.500ms="buscar" placeholder="Nº acta, dominio, DNI o nombre" class="{{ $inputClass }}">
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- INDICADORES · ACTAS (responden a los filtros de arriba)           --}}
    {{-- ================================================================ --}}
    <div class="mb-6">
        <div class="flex items-baseline gap-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actas por origen</h3>
            <span class="text-xs text-gray-400 dark:text-gray-500">según los filtros de arriba</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 {{ $hayOtros ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-3">
            @foreach(array_merge([
                ['Total actas', $total, null, 'text-gray-900 dark:text-gray-100'],
                ['Cámaras', $totalCamaras, $porcentaje($totalCamaras).'%', 'text-blue-600 dark:text-blue-400'],
                ['Manuales', $totalManuales, $porcentaje($totalManuales).'%', 'text-green-600 dark:text-green-400'],
            ], $hayOtros ? [
                ['Otros', $totalOtros, $porcentaje($totalOtros).'%', 'text-amber-600 dark:text-amber-400'],
            ] : [], [
                ['Bajas', (int) $totales->bajas, $porcentaje((int) $totales->bajas).'%', 'text-red-600 dark:text-red-400'],
            ]) as [$titulo, $valor, $detalle, $color])
                <div class="{{ $cardClass }}">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $titulo }}</div>
                    <div class="mt-1 text-2xl font-bold {{ $color }}">{{ number_format($valor, 0, ',', '.') }}</div>
                    @if($detalle)
                        <div class="text-xs text-gray-400 dark:text-gray-500">{{ $detalle }} del total</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- INDICADORES · MEDIDAS PREVENTIVAS (filtro propio, independiente)  --}}
    {{-- ================================================================ --}}
    <div class="{{ $cardClass }} mb-6">
        <div class="flex flex-wrap items-end justify-between gap-3 mb-3">
            <div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Medidas preventivas</h3>
                    <span wire:loading.delay wire:target="filterMedidasQuick,filterMedidasFechaDesde,filterMedidasFechaHasta" class="text-xs text-gray-400">Cargando…</span>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    Medidas tomadas en las actas del período elegido acá abajo. Tiene su propio filtro:
                    no depende del origen ni del resto de los filtros de arriba.
                </p>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="{{ $labelClass }}">Período</label>
                    <select wire:model.live="filterMedidasQuick" class="{{ $inputClass }}">
                        <option value="">— Personalizado —</option>
                        <option value="hoy">Hoy</option>
                        <option value="semana">Esta semana</option>
                        <option value="mes">Este mes</option>
                        <option value="anio">Este año</option>
                        <option value="12meses">Últimos 12 meses</option>
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}">Desde</label>
                    <input type="date" wire:model.live="filterMedidasFechaDesde" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Hasta</label>
                    <input type="date" wire:model.live="filterMedidasFechaHasta" class="{{ $inputClass }}">
                </div>
                <button wire:click="limpiarFiltrosMedidas" type="button"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Reiniciar
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach([
                ['Secuestros', (int) $medidas->secuestros, 'text-orange-600 dark:text-orange-400'],
                ['Decomisos', (int) $medidas->decomisos, 'text-purple-600 dark:text-purple-400'],
                ['Clausuras', (int) $medidas->clausuras, 'text-red-600 dark:text-red-400'],
                ['Retenciones de licencia', (int) $medidas->retenciones, 'text-indigo-600 dark:text-indigo-400'],
            ] as [$titulo, $valor, $color])
                <div class="rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 px-3 py-2.5">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $titulo }}</div>
                    <div class="text-xl font-bold leading-tight {{ $color }}">{{ number_format($valor, 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500">
                        {{ $medidasTotal > 0 ? round($valor * 100 / $medidasTotal, 1) : 0 }}% de las actas del período
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-xs font-medium text-gray-700 dark:text-gray-200 mt-2.5">
            {{ number_format($medidasConMedida, 0, ',', '.') }} de {{ number_format($medidasTotal, 0, ',', '.') }} actas
            del período tienen al menos una medida preventiva
            ({{ $medidasTotal > 0 ? round($medidasConMedida * 100 / $medidasTotal, 1) : 0 }}%).
            <span class="font-normal text-gray-500 dark:text-gray-400">Una misma acta puede tener más de una.</span>
        </p>
    </div>

    {{-- ================================================================ --}}
    {{-- GRÁFICOS                                                          --}}
    {{-- ================================================================ --}}
    <div class="{{ $cardClass }} mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
            Actas por {{ ['dia' => 'día', 'mes' => 'mes', 'anio' => 'año'][$charts['periodo']['agrupacion']] }}
        </h3>
        <div wire:ignore><div id="chartActasPeriodo" style="min-height: 320px;"></div></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="{{ $cardClass }}">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Top 15 inspectores</h3>
            <div wire:ignore><div id="chartActasInspector" style="min-height: 420px;"></div></div>
        </div>
        <div class="{{ $cardClass }}">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Top 10 motivos de infracción</h3>
            <div wire:ignore><div id="chartActasMotivo" style="min-height: 420px;"></div></div>
        </div>
        <div class="{{ $cardClass }}">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Por hora del día</h3>
            <div wire:ignore><div id="chartActasHora" style="min-height: 280px;"></div></div>
        </div>
        <div class="{{ $cardClass }}">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Por día de la semana</h3>
            <div wire:ignore><div id="chartActasDiaSemana" style="min-height: 280px;"></div></div>
        </div>
        <div class="{{ $cardClass }}">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Por departamento</h3>
            <div wire:ignore><div id="chartActasDepartamento" style="min-height: 300px;"></div></div>
        </div>
        <div class="{{ $cardClass }}">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Por estado</h3>
            <div wire:ignore><div id="chartActasEstado" style="min-height: 300px;"></div></div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- CÁMARAS                                                           --}}
    {{-- ================================================================ --}}
    <div class="{{ $cardClass }} mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Actas de cámaras</h3>
        @if((int) ($camaras->total ?? 0) === 0)
            <p class="text-sm text-gray-400 dark:text-gray-500">No hay actas de cámaras con los filtros seleccionados.</p>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="space-y-4">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actas con preacta</div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($camaras->total, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Preactas confirmadas</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ number_format((int) $camaras->confirmadas, 0, ',', '.') }}
                            <span class="text-sm font-normal text-gray-400">({{ round($camaras->confirmadas * 100 / $camaras->total, 1) }}%)</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Demora promedio captura → acta</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $camaras->demora_promedio !== null ? number_format($camaras->demora_promedio, 1, ',', '.') : '-' }}
                            <span class="text-sm font-normal text-gray-400">días</span>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Top 10 lugares de captura</div>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($lugaresCamaras as $lugar)
                                <tr>
                                    <td class="py-1.5 text-gray-700 dark:text-gray-300">{{ $lugar->lugar }}</td>
                                    <td class="py-1.5 w-48">
                                        <div class="h-2 rounded bg-blue-500" style="width: {{ max(2, round($lugar->total * 100 / $lugaresCamaras->max('total'))) }}%"></div>
                                    </td>
                                    <td class="py-1.5 pl-3 text-right font-semibold text-gray-900 dark:text-gray-100 w-16">{{ number_format($lugar->total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- ================================================================ --}}
    {{-- LISTADO                                                           --}}
    {{-- ================================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Listado de actas</h3>
            <span class="ml-auto text-xs text-gray-400">
                <span class="hidden md:inline text-gray-400 dark:text-gray-500">Clic en un acta para ver el detalle · </span>{{ number_format($actas->total(), 0, ',', '.') }} resultados
            </span>
        </div>
        <div class="overflow-x-auto" data-scroll-actas>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-4 py-2">Nº Acta</th>
                        <th class="px-4 py-2">Fecha</th>
                        <th class="px-4 py-2">Origen</th>
                        <th class="px-4 py-2">Infractor</th>
                        <th class="px-4 py-2">Motivos</th>
                        <th class="px-4 py-2">Dominio</th>
                        <th class="px-4 py-2">Lugar</th>
                        <th class="px-4 py-2">Inspector</th>
                        <th class="px-4 py-2">Estado</th>
                        {{-- Columna de la flecha: fijada al borde derecho visible --}}
                        <th class="sticky right-0 w-16 pl-2 pr-4 py-2 bg-gray-50 dark:bg-gray-700"></th>
                    </tr>
                </thead>
                @forelse($actas as $acta)
                    {{-- Un tbody por acta: la fila de detalle es hermana de la fila del acta
                         y comparten el estado de Alpine del desplegable. --}}
                    <tbody class="border-t border-gray-100 dark:border-gray-700"
                           wire:key="acta-{{ $acta->id }}"
                           x-data="{ detalle: false, fotoActual: null, indiceActual: 0, fotos: [] }"
                           x-init="fotos = JSON.parse($el.dataset.fotos || '[]')"
                           data-fotos="{{ json_encode($acta->fotos) }}">
                        <tr @click="detalle = !detalle"
                            class="group cursor-pointer transition-colors"
                            :class="detalle ? 'bg-indigo-50 dark:bg-indigo-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30'">
                            <td class="px-4 py-2 font-mono text-gray-900 dark:text-gray-100">
                                {{ $acta->actanro }}
                                <div class="text-xs text-gray-400 font-sans">{{ $acta->departamento_nombre }}</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}
                                <div class="text-xs text-gray-400">{{ substr($acta->hora, 0, 5) }}</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @if($acta->preacta_id > 0)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"
                                          title="Preacta #{{ $acta->preacta_id }}{{ $acta->preacta_fecha ? ' — captura '.\Carbon\Carbon::parse($acta->preacta_fecha)->format('d/m/Y').' '.substr($acta->preacta_hora, 0, 5) : '' }}{{ $acta->preacta_lugar ? ' en '.$acta->preacta_lugar : '' }}">
                                        Cámara
                                    </span>
                                    <div class="text-xs text-gray-400 mt-0.5">Preacta #{{ $acta->preacta_id }}{{ $acta->preacta_confirmada ? ' ✓' : '' }}</div>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">Manual</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                                {{ $acta->nombreinf ?: '-' }}
                                @if($acta->dni)<div class="text-xs text-gray-400">DNI {{ $acta->dni }}</div>@endif
                            </td>
                            <td class="px-4 py-2 min-w-[14rem]">
                                @forelse($acta->motivos as $motivo)
                                    <span class="inline-block max-w-[16rem] truncate align-top mb-1 px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300" title="{{ $motivo }}">{{ $motivo }}</span>
                                @empty
                                    <span class="text-gray-400">-</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-2 font-mono text-gray-700 dark:text-gray-300">{{ $acta->dominio ?: '-' }}</td>
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $acta->lugarinfra ?: '-' }}</td>
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $acta->inspector_nombre ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $acta->estado == 5 ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $estados[$acta->estado] ?? $acta->estado }}
                                </span>
                            </td>
                            {{-- La flecha va a la derecha, como en Estadísticas Operativos, pero
                                 fijada al borde visible: la tabla puede ser más ancha que el
                                 contenedor y con el scroll horizontal quedaba cortada.
                                 La celda necesita fondo propio (es sticky) y lo copia de la fila. --}}
                            <td class="sticky right-0 w-16 pl-2 pr-4 py-2 align-middle transition-colors"
                                :class="detalle ? 'bg-indigo-50 dark:bg-indigo-900/20' : 'bg-white dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-700/30'">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(count($acta->fotos) > 0)
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-sky-100 text-sky-600 dark:bg-sky-900/40 dark:text-sky-400 flex-shrink-0" title="{{ count($acta->fotos) }} foto(s)">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </span>
                                    @endif
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0"
                                         :class="detalle ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </td>
                        </tr>

                        {{-- DETALLE DEL ACTA --}}
                        <tr x-show="detalle" x-cloak
                            class="bg-slate-50 dark:bg-gray-900/40 border-t border-indigo-100 dark:border-indigo-900/30">
                            <td colspan="10" class="p-0">
                                {{-- La celda mide lo que mide la tabla, que puede ser más ancha que
                                     la pantalla. El panel se fija al ancho visible y acompaña el
                                     scroll horizontal, así las tarjetas nunca quedan fuera de vista. --}}
                                <div class="sticky left-0 px-6 py-4"
                                     x-init="$nextTick(() => { const c = $el.closest('[data-scroll-actas]'); if (c?.clientWidth) $el.style.width = c.clientWidth + 'px'; })"
                                     @resize.window="const c = $el.closest('[data-scroll-actas]'); if (c?.clientWidth) $el.style.width = c.clientWidth + 'px'">
                                {{-- Dos tarjetas por fila y cada una con sus datos en dos columnas:
                                     así el detalle queda bajo y aprovecha el ancho de la pantalla. --}}
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 text-xs items-start">

                                    @if($acta->motivos->isNotEmpty())
                                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg p-3 border border-indigo-100 dark:border-indigo-900/40">
                                        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Motivos de Infracción</div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($acta->motivos as $motivo)
                                                <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 text-xs font-medium">{{ $motivo }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-100 dark:border-gray-700">
                                        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Infractor</div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-gray-700 dark:text-gray-300 break-words">
                                            <div><span class="text-gray-400">Nombre:</span> {{ $acta->nombreinf ?: '—' }}</div>
                                            <div><span class="text-gray-400">DNI:</span> {{ $acta->dni ? number_format($acta->dni, 0, ',', '.') : '—' }}</div>
                                            <div><span class="text-gray-400">Domicilio:</span> {{ $acta->direcinf ?: '—' }}</div>
                                            <div><span class="text-gray-400">N° Licencia:</span> {{ $acta->licencia ?: '—' }}</div>
                                        </div>
                                    </div>

                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-100 dark:border-gray-700">
                                        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Vehículo</div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-gray-700 dark:text-gray-300 break-words">
                                            <div><span class="text-gray-400">Dominio:</span> <span class="font-mono font-bold">{{ $acta->dominio ?: '—' }}</span></div>
                                            <div><span class="text-gray-400">Marca:</span> {{ $acta->marca_vehiculo ?: '—' }}</div>
                                            <div><span class="text-gray-400">Tipo:</span> {{ $acta->tipo_rodado ?: '—' }}</div>
                                            <div><span class="text-gray-400">Modelo:</span> {{ $acta->modelo ?: '—' }}</div>
                                            @if($acta->chasis)
                                                <div><span class="text-gray-400">Chasis:</span> {{ $acta->chasis }}</div>
                                            @endif
                                            @if($acta->motor)
                                                <div><span class="text-gray-400">Motor:</span> {{ $acta->motor }}</div>
                                            @endif
                                            @if($acta->grad_alcohol > 0)
                                                <div class="text-red-600 dark:text-red-400 font-semibold">
                                                    <span class="text-gray-400 font-normal">Alcohol:</span> {{ $acta->grad_alcohol }}‰
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-100 dark:border-gray-700">
                                        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Medidas e Inspector</div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-gray-700 dark:text-gray-300 break-words">
                                            <div class="sm:col-span-2 flex flex-wrap gap-1 mb-1">
                                                @if($acta->secuestro) <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">Secuestro</span> @endif
                                                @if($acta->decomiso) <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">Decomiso</span> @endif
                                                @if($acta->clausura) <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">Clausura</span> @endif
                                                @if($acta->retiene_lic) <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">Ret. Licencia</span> @endif
                                                @if(!$acta->secuestro && !$acta->decomiso && !$acta->clausura && !$acta->retiene_lic)
                                                    <span class="text-gray-300 dark:text-gray-600">Sin medidas preventivas</span>
                                                @endif
                                            </div>
                                            <div><span class="text-gray-400">Inspector:</span> {{ $acta->inspector_nombre ?: '—' }}</div>
                                            <div><span class="text-gray-400">Departamento:</span> {{ $acta->departamento_nombre ?: '—' }}</div>
                                            <div><span class="text-gray-400">Lugar:</span> {{ $acta->lugarinfra ?: '—' }}</div>
                                            <div>
                                                <span class="text-gray-400">Estado:</span> {{ $estados[$acta->estado] ?? $acta->estado }}
                                                @if($acta->motivo_baja)
                                                    <span class="text-red-500 dark:text-red-400">({{ $acta->motivo_baja }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-100 dark:border-gray-700">
                                        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Origen y carga</div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-gray-700 dark:text-gray-300 break-words">
                                            @if($acta->preacta_id > 0)
                                                <div><span class="text-gray-400">Origen:</span> Cámara (preacta #{{ $acta->preacta_id }})</div>
                                                <div><span class="text-gray-400">Preacta confirmada:</span> {{ $acta->preacta_confirmada ? 'Sí' : 'No' }}</div>
                                                @if($acta->preacta_fecha)
                                                    <div><span class="text-gray-400">Captura:</span> {{ \Carbon\Carbon::parse($acta->preacta_fecha)->format('d/m/Y') }} {{ substr($acta->preacta_hora ?? '', 0, 5) }}</div>
                                                @endif
                                                @if($acta->preacta_lugar)
                                                    <div><span class="text-gray-400">Lugar de captura:</span> {{ $acta->preacta_lugar }}</div>
                                                @endif
                                            @else
                                                <div class="sm:col-span-2"><span class="text-gray-400">Origen:</span> Manual{{ $acta->operativo_id == -1 ? ' (sistema de actas)' : '' }}</div>
                                            @endif
                                            <div><span class="text-gray-400">Cargada por:</span> {{ $acta->crea_user ?: '—' }}</div>
                                            <div><span class="text-gray-400">Fecha de carga:</span> {{ $acta->crea_fecha ? \Carbon\Carbon::parse($acta->crea_fecha)->format('d/m/Y') : '—' }}</div>
                                        </div>
                                    </div>

                                    @if($acta->nombreden || $acta->dniden || $acta->direcden)
                                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-100 dark:border-gray-700">
                                        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Denunciante</div>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-1 text-gray-700 dark:text-gray-300 break-words">
                                            <div><span class="text-gray-400">Nombre:</span> {{ $acta->nombreden ?: '—' }}</div>
                                            <div><span class="text-gray-400">DNI:</span> {{ $acta->dniden ? number_format($acta->dniden, 0, ',', '.') : '—' }}</div>
                                            <div><span class="text-gray-400">Domicilio:</span> {{ $acta->direcden ?: '—' }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($acta->obs)
                                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-100 dark:border-gray-700">
                                        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Observaciones</div>
                                        <div class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $acta->obs }}</div>
                                    </div>
                                    @endif

                                    @if(count($acta->fotos) > 0)
                                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg p-3 border border-sky-100 dark:border-sky-900/40">
                                        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Fotos ({{ count($acta->fotos) }})
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($acta->fotos as $idx => $fotoUrl)
                                            <button type="button"
                                                    @click.stop="indiceActual = {{ $idx }}; fotoActual = fotos[{{ $idx }}]"
                                                    class="relative group overflow-hidden rounded-md w-20 h-20 flex-shrink-0 border-2 border-sky-200 dark:border-sky-700 hover:border-sky-400 dark:hover:border-sky-500 transition-colors focus:outline-none focus:ring-2 focus:ring-sky-400">
                                                <img src="{{ $fotoUrl }}"
                                                     alt="Foto {{ $idx + 1 }} acta #{{ $acta->actanro }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                                     loading="lazy">
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-colors flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 drop-shadow transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zm-3-4v1m0 3h.01"/>
                                                    </svg>
                                                </div>
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                </div>

                                {{-- Lightbox de fotos --}}
                                <div x-show="fotoActual !== null"
                                     x-cloak
                                     class="fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center"
                                     @click.self="fotoActual = null"
                                     @keydown.escape.window="fotoActual = null"
                                     @keydown.arrow-left.window="if(fotoActual !== null && indiceActual > 0){ indiceActual--; fotoActual = fotos[indiceActual]; }"
                                     @keydown.arrow-right.window="if(fotoActual !== null && indiceActual < fotos.length - 1){ indiceActual++; fotoActual = fotos[indiceActual]; }">

                                    <button type="button" @click="fotoActual = null"
                                            class="absolute top-4 right-4 z-10 text-white bg-black/50 rounded-full p-2 hover:bg-black/80 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>

                                    <button type="button"
                                            x-show="fotos.length > 1"
                                            @click="if(indiceActual > 0){ indiceActual--; fotoActual = fotos[indiceActual]; }"
                                            :class="indiceActual === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-black/80'"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 z-10 text-white bg-black/50 rounded-full p-3 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>

                                    <img :src="fotoActual"
                                         class="max-w-[90vw] max-h-[85vh] object-contain rounded shadow-2xl select-none">

                                    <button type="button"
                                            x-show="fotos.length > 1"
                                            @click="if(indiceActual < fotos.length - 1){ indiceActual++; fotoActual = fotos[indiceActual]; }"
                                            :class="indiceActual === fotos.length - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-black/80'"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 z-10 text-white bg-black/50 rounded-full p-3 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>

                                    <div x-show="fotos.length > 1"
                                         class="absolute bottom-4 left-0 right-0 flex justify-center items-center gap-3">
                                        <span class="text-white/70 text-sm font-mono tabular-nums" x-text="(indiceActual + 1) + ' / ' + fotos.length"></span>
                                    </div>
                                </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="10" class="px-4 py-10 text-center text-gray-400 dark:text-gray-500">No hay actas con los filtros seleccionados.</td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>
        <div class="px-4 py-3">
            {{ $actas->links('vendor.livewire.tailwind-sin-resumen') }}
        </div>
    </div>
</div>

@script
<script>
    const oscuro = () => window.matchMedia('(prefers-color-scheme: dark)').matches;
    const COLOR_CAMARAS = '#144BE9';
    const COLOR_MANUALES = '#77BF43';
    const COLOR_OTROS = '#E8912D';

    // La serie "Otros" solo se suma si el backend la mandó (charts.hayOtros).
    function serieOtros(datos) {
        return datos ? [{ name: 'Otros', data: datos }] : [];
    }
    function colores(hayOtros) {
        return hayOtros ? [COLOR_CAMARAS, COLOR_MANUALES, COLOR_OTROS] : [COLOR_CAMARAS, COLOR_MANUALES];
    }
    let graficos = {};

    function base(tipo, alto, extra = {}) {
        return {
            chart: { type: tipo, height: alto, toolbar: { show: false }, background: 'transparent', fontFamily: 'inherit' },
            theme: { mode: oscuro() ? 'dark' : 'light' },
            dataLabels: { enabled: false },
            noData: { text: 'Sin datos' },
            ...extra,
        };
    }

    function opciones(charts) {
        return {
            chartActasPeriodo: base('bar', 320, {
                chart: { type: 'bar', height: 320, stacked: true, toolbar: { show: true }, background: 'transparent', fontFamily: 'inherit' },
                series: [
                    { name: 'Cámaras', data: charts.periodo.camaras },
                    { name: 'Manuales', data: charts.periodo.manuales },
                    ...serieOtros(charts.periodo.otros),
                ],
                colors: colores(charts.hayOtros),
                xaxis: { categories: charts.periodo.labels, labels: { rotate: -45, hideOverlappingLabels: true } },
            }),
            chartActasInspector: base('bar', 420, {
                chart: { type: 'bar', height: 420, stacked: true, toolbar: { show: false }, background: 'transparent', fontFamily: 'inherit' },
                plotOptions: { bar: { horizontal: true } },
                series: [
                    { name: 'Cámaras', data: charts.inspector.camaras },
                    { name: 'Manuales', data: charts.inspector.manuales },
                    ...serieOtros(charts.inspector.otros),
                ],
                colors: colores(charts.hayOtros),
                xaxis: { categories: charts.inspector.labels },
            }),
            chartActasMotivo: base('bar', 420, {
                plotOptions: { bar: { horizontal: true } },
                series: [{ name: 'Actas', data: charts.motivo.data }],
                colors: ['#FFA500'],
                xaxis: { categories: charts.motivo.labels },
                yaxis: { labels: { maxWidth: 260 } },
            }),
            chartActasHora: base('bar', 280, {
                series: [{ name: 'Actas', data: charts.hora.data }],
                colors: ['#4ECDC4'],
                xaxis: { categories: charts.hora.labels, labels: { rotate: -45 } },
            }),
            chartActasDiaSemana: base('bar', 280, {
                series: [{ name: 'Actas', data: charts.diaSemana.data }],
                colors: ['#144BE9'],
                xaxis: { categories: charts.diaSemana.labels },
            }),
            chartActasDepartamento: base('bar', 300, {
                plotOptions: { bar: { horizontal: true } },
                series: [{ name: 'Actas', data: charts.departamento.data }],
                colors: ['#77BF43'],
                xaxis: { categories: charts.departamento.labels },
            }),
            chartActasEstado: base('donut', 300, {
                series: charts.estado.data,
                labels: charts.estado.labels,
                colors: ['#FFA500', '#4ECDC4', '#77BF43', '#9CA3AF', '#FF6B6B'],
                dataLabels: { enabled: true },
                legend: { position: 'bottom' },
            }),
        };
    }

    function dibujar(charts) {
        if (typeof window.ApexCharts === 'undefined') return;
        Object.entries(opciones(charts)).forEach(([id, opts]) => {
            const el = document.getElementById(id);
            if (!el) return;
            if (graficos[id]) graficos[id].destroy();
            graficos[id] = new window.ApexCharts(el, opts);
            graficos[id].render();
        });
    }

    dibujar(@js($charts));

    $wire.on('actas-actualizadas', ({ charts }) => dibujar(charts));
</script>
@endscript
