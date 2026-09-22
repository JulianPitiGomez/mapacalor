@php
    $inputClass = 'w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm';
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1';
    $cardClass = 'bg-white dark:bg-gray-800 p-4 rounded-lg shadow';
    $total = (int) ($totales->total ?? 0);
    $totalCamaras = (int) ($totales->camaras ?? 0);
    $porcentaje = fn ($n) => $total > 0 ? round($n * 100 / $total, 1) : 0;
@endphp

<div>
    {{-- ================================================================ --}}
    {{-- FILTROS                                                           --}}
    {{-- ================================================================ --}}
    <div class="mb-6 {{ $cardClass }}">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Filtros</h3>
            <div class="flex items-center gap-3">
                <span wire:loading class="text-xs text-gray-400">Cargando…</span>
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
                    <option value="manuales">Manuales (sin preacta)</option>
                </select>
            </div>
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
    {{-- INDICADORES                                                       --}}
    {{-- ================================================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
        @foreach([
            ['Total actas', $total, null, 'text-gray-900 dark:text-gray-100'],
            ['Cámaras', $totalCamaras, $porcentaje($totalCamaras).'%', 'text-blue-600 dark:text-blue-400'],
            ['Manuales', $total - $totalCamaras, $porcentaje($total - $totalCamaras).'%', 'text-green-600 dark:text-green-400'],
            ['Secuestros', (int) $totales->secuestros, null, 'text-gray-900 dark:text-gray-100'],
            ['Retención lic.', (int) $totales->retiene_lic, null, 'text-gray-900 dark:text-gray-100'],
            ['Decomisos / Clausuras', (int) $totales->decomisos.' / '.(int) $totales->clausuras, null, 'text-gray-900 dark:text-gray-100'],
            ['Bajas', (int) $totales->bajas, $porcentaje((int) $totales->bajas).'%', 'text-red-600 dark:text-red-400'],
        ] as [$titulo, $valor, $detalle, $color])
            <div class="{{ $cardClass }}">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $titulo }}</div>
                <div class="mt-1 text-2xl font-bold {{ $color }}">{{ is_int($valor) ? number_format($valor, 0, ',', '.') : $valor }}</div>
                @if($detalle)
                    <div class="text-xs text-gray-400 dark:text-gray-500">{{ $detalle }} del total</div>
                @endif
            </div>
        @endforeach
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
            <span class="ml-auto text-xs text-gray-400">{{ number_format($actas->total(), 0, ',', '.') }} resultados</span>
        </div>
        <div class="overflow-x-auto">
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($actas as $acta)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30" wire:key="acta-{{ $acta->id }}">
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-gray-400 dark:text-gray-500">No hay actas con los filtros seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
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
                ],
                colors: [COLOR_CAMARAS, COLOR_MANUALES],
                xaxis: { categories: charts.periodo.labels, labels: { rotate: -45, hideOverlappingLabels: true } },
            }),
            chartActasInspector: base('bar', 420, {
                chart: { type: 'bar', height: 420, stacked: true, toolbar: { show: false }, background: 'transparent', fontFamily: 'inherit' },
                plotOptions: { bar: { horizontal: true } },
                series: [
                    { name: 'Cámaras', data: charts.inspector.camaras },
                    { name: 'Manuales', data: charts.inspector.manuales },
                ],
                colors: [COLOR_CAMARAS, COLOR_MANUALES],
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
