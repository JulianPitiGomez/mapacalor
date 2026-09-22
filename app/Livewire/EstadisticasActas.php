<?php

namespace App\Livewire;

use App\Models\Departamento;
use App\Models\Inspector;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class EstadisticasActas extends Component
{
    use WithPagination;

    public string $filterFechaDesde = '';

    public string $filterFechaHasta = '';

    public string $filterQuick = 'mes';

    public string $filterInspector = '';

    public string $filterDepartamento = '';

    public string $filterOrigen = '';

    public string $filterEstado = '';

    public string $buscar = '';

    // fa_acta_estado
    public const ESTADOS = [
        1 => 'Iniciada',
        2 => 'Activa',
        3 => 'Aprobada',
        4 => 'Vencida',
        5 => 'Baja',
    ];

    public function mount(): void
    {
        $this->filterFechaDesde = now()->startOfMonth()->format('Y-m-d');
        $this->filterFechaHasta = now()->format('Y-m-d');
    }

    public function updatingFilterFechaDesde(): void
    {
        $this->filterQuick = '';
    }

    public function updatingFilterFechaHasta(): void
    {
        $this->filterQuick = '';
    }

    public function updatingFilterQuick(string $value): void
    {
        switch ($value) {
            case 'hoy':
                $this->filterFechaDesde = now()->format('Y-m-d');
                break;
            case 'semana':
                $this->filterFechaDesde = now()->startOfWeek()->format('Y-m-d');
                break;
            case 'mes':
                $this->filterFechaDesde = now()->startOfMonth()->format('Y-m-d');
                break;
            case 'anio':
                $this->filterFechaDesde = now()->startOfYear()->format('Y-m-d');
                break;
            case '12meses':
                $this->filterFechaDesde = now()->subMonths(12)->addDay()->format('Y-m-d');
                break;
            default:
                return;
        }
        $this->filterFechaHasta = now()->format('Y-m-d');
    }

    public function updated(string $propiedad): void
    {
        if (str_starts_with($propiedad, 'filter') || $propiedad === 'buscar') {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['filterInspector', 'filterDepartamento', 'filterOrigen', 'filterEstado', 'buscar']);
        $this->filterQuick = 'mes';
        $this->updatingFilterQuick('mes');
        $this->resetPage();
    }

    /**
     * Query base sobre fa_acta con todos los filtros aplicados.
     * Solo actas simples: las de operativos (operativo_id > 0; en faltas el 0 significa
     * "sin operativo") ya se ven en Estadísticas Operativos.
     * Las actas de cámaras son las que vienen de una preacta (preacta_id > 0).
     */
    private function actasFiltradas()
    {
        $query = DB::connection('mysql_faltas')->table('fa_acta as a')
            ->where(fn ($q) => $q->whereNull('a.operativo_id')->orWhere('a.operativo_id', '<=', 0));

        if ($this->filterFechaDesde) {
            $query->where('a.fecha', '>=', $this->filterFechaDesde);
        }
        if ($this->filterFechaHasta) {
            $query->where('a.fecha', '<=', $this->filterFechaHasta);
        }
        if ($this->filterInspector !== '') {
            $query->where('a.inspector_id', (int) $this->filterInspector);
        }
        if ($this->filterDepartamento !== '') {
            $query->where('a.dto_id', (int) $this->filterDepartamento);
        }
        if ($this->filterOrigen === 'camaras') {
            $query->where('a.preacta_id', '>', 0);
        } elseif ($this->filterOrigen === 'manuales') {
            $query->where(fn ($q) => $q->whereNull('a.preacta_id')->orWhere('a.preacta_id', '<=', 0));
        }
        if ($this->filterEstado !== '') {
            $query->where('a.estado', (int) $this->filterEstado);
        }
        if (trim($this->buscar) !== '') {
            $texto = trim($this->buscar);
            $query->where(function ($q) use ($texto) {
                $q->where('a.dominio', 'like', "%{$texto}%")
                    ->orWhere('a.nombreinf', 'like', "%{$texto}%");
                if (ctype_digit($texto)) {
                    $q->orWhere('a.actanro', (int) $texto)
                        ->orWhere('a.dni', (int) $texto);
                }
            });
        }

        return $query;
    }

    public function render()
    {
        $esCamara = 'CASE WHEN a.preacta_id > 0 THEN 1 ELSE 0 END';

        $totales = $this->actasFiltradas()
            ->selectRaw("COUNT(*) as total,
                SUM({$esCamara}) as camaras,
                SUM(a.secuestro) as secuestros,
                SUM(a.decomiso) as decomisos,
                SUM(a.clausura) as clausuras,
                SUM(a.retiene_lic) as retiene_lic,
                SUM(CASE WHEN a.estado = 5 THEN 1 ELSE 0 END) as bajas,
                COUNT(DISTINCT a.inspector_id) as inspectores")
            ->first();

        // Evolución temporal: agrupa por día, mes o año según el largo del rango
        $agrupacion = $this->agrupacionTemporal();
        $formatoSql = ['dia' => '%Y-%m-%d', 'mes' => '%Y-%m', 'anio' => '%Y'][$agrupacion];
        $porPeriodo = $this->actasFiltradas()
            ->selectRaw("DATE_FORMAT(a.fecha, '{$formatoSql}') as periodo, COUNT(*) as total, SUM({$esCamara}) as camaras")
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get();

        $porInspector = $this->actasFiltradas()
            ->leftJoin('fa_inspector as i', 'i.id', '=', 'a.inspector_id')
            ->selectRaw("a.inspector_id, COALESCE(i.nombre, CONCAT('#', a.inspector_id)) as nombre, COUNT(*) as total, SUM({$esCamara}) as camaras")
            ->groupBy('a.inspector_id', 'i.nombre')
            ->orderByDesc('total')
            ->limit(15)
            ->get();

        $porDepartamento = $this->actasFiltradas()
            ->leftJoin('fa_departamento as d', 'd.id', '=', 'a.dto_id')
            ->selectRaw("COALESCE(d.nombre, CONCAT('#', a.dto_id)) as nombre, COUNT(*) as total")
            ->groupBy('a.dto_id', 'd.nombre')
            ->orderByDesc('total')
            ->get();

        // STRAIGHT_JOIN: sin él MySQL arranca por fa_motivo/fa_acta_motivo y recorre las
        // ~250k filas de motivos (10 s); forzando fa_acta primero usa el filtro de fecha.
        $porMotivo = $this->actasFiltradas()
            ->join('fa_acta_motivo as am', 'am.acta_id', '=', 'a.id')
            ->selectRaw('STRAIGHT_JOIN am.motivo_id, COUNT(DISTINCT a.id) as total')
            ->groupBy('am.motivo_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $nombresMotivos = DB::connection('mysql_faltas')->table('fa_motivo')
            ->whereIn('id', $porMotivo->pluck('motivo_id'))
            ->pluck('nombre', 'id');
        $porMotivo->each(fn ($r) => $r->nombre = $nombresMotivos[$r->motivo_id] ?? "#{$r->motivo_id}");

        $porHora = $this->actasFiltradas()
            ->selectRaw('HOUR(a.hora) as hora, COUNT(*) as total')
            ->groupBy('hora')
            ->pluck('total', 'hora');

        // DAYOFWEEK: 1 = domingo … 7 = sábado
        $porDiaSemana = $this->actasFiltradas()
            ->selectRaw('DAYOFWEEK(a.fecha) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $porEstado = $this->actasFiltradas()
            ->selectRaw('a.estado, COUNT(*) as total')
            ->groupBy('a.estado')
            ->pluck('total', 'estado');

        // Cámaras: datos de la preacta asociada (lugar de captura y demora hasta labrar el acta)
        $camaras = $this->actasFiltradas()
            ->join('fa_preacta as p', 'p.ID', '=', 'a.preacta_id')
            ->selectRaw('COUNT(*) as total,
                SUM(p.CONFIRMADA) as confirmadas,
                AVG(DATEDIFF(a.crea_fecha, p.FECHA)) as demora_promedio')
            ->first();

        $lugaresCamaras = $this->actasFiltradas()
            ->join('fa_preacta as p', 'p.ID', '=', 'a.preacta_id')
            ->selectRaw("COALESCE(NULLIF(TRIM(p.LUGARINFRA), ''), 'Sin lugar') as lugar, COUNT(*) as total")
            ->groupBy('lugar')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $actas = $this->actasFiltradas()
            ->leftJoin('fa_inspector as i', 'i.id', '=', 'a.inspector_id')
            ->leftJoin('fa_departamento as d', 'd.id', '=', 'a.dto_id')
            ->leftJoin('fa_preacta as p', 'p.ID', '=', 'a.preacta_id')
            ->select(
                'a.id', 'a.actanro', 'a.fecha', 'a.hora', 'a.nombreinf', 'a.dni', 'a.dominio',
                'a.lugarinfra', 'a.estado', 'a.preacta_id',
                'a.secuestro', 'a.decomiso', 'a.clausura', 'a.retiene_lic',
                DB::raw('i.nombre as inspector_nombre'),
                DB::raw('d.nombre as departamento_nombre'),
                DB::raw('p.FECHA as preacta_fecha'),
                DB::raw('p.HORA as preacta_hora'),
                DB::raw('p.LUGARINFRA as preacta_lugar'),
                DB::raw('p.CONFIRMADA as preacta_confirmada')
            )
            ->orderByDesc('a.fecha')
            ->orderByDesc('a.hora')
            ->paginate(15);

        // Motivos solo de las actas de la página (por índice de acta_id, sin joins en la paginada)
        $motivosPorActa = DB::connection('mysql_faltas')->table('fa_acta_motivo as am')
            ->join('fa_motivo as mo', 'mo.id', '=', 'am.motivo_id')
            ->whereIn('am.acta_id', $actas->pluck('id'))
            ->orderBy('mo.nombre')
            ->get(['am.acta_id', 'mo.nombre'])
            ->groupBy('acta_id');
        $actas->getCollection()->each(
            fn ($acta) => $acta->motivos = $motivosPorActa->get($acta->id, collect())->pluck('nombre')->unique()->values()
        );

        $charts = [
            'periodo' => [
                'agrupacion' => $agrupacion,
                'labels' => $porPeriodo->map(fn ($r) => $this->etiquetaPeriodo($r->periodo, $agrupacion))->values(),
                'camaras' => $porPeriodo->map(fn ($r) => (int) $r->camaras)->values(),
                'manuales' => $porPeriodo->map(fn ($r) => (int) $r->total - (int) $r->camaras)->values(),
            ],
            'inspector' => [
                'labels' => $porInspector->pluck('nombre')->values(),
                'camaras' => $porInspector->map(fn ($r) => (int) $r->camaras)->values(),
                'manuales' => $porInspector->map(fn ($r) => (int) $r->total - (int) $r->camaras)->values(),
            ],
            'departamento' => [
                'labels' => $porDepartamento->pluck('nombre')->values(),
                'data' => $porDepartamento->map(fn ($r) => (int) $r->total)->values(),
            ],
            'motivo' => [
                'labels' => $porMotivo->pluck('nombre')->values(),
                'data' => $porMotivo->map(fn ($r) => (int) $r->total)->values(),
            ],
            'hora' => [
                'labels' => collect(range(0, 23))->map(fn ($h) => str_pad($h, 2, '0', STR_PAD_LEFT).'h'),
                'data' => collect(range(0, 23))->map(fn ($h) => (int) ($porHora[$h] ?? 0)),
            ],
            'diaSemana' => [
                // Reordenado a lunes → domingo
                'labels' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                'data' => collect([2, 3, 4, 5, 6, 7, 1])->map(fn ($d) => (int) ($porDiaSemana[$d] ?? 0)),
            ],
            'estado' => [
                'labels' => $porEstado->keys()->map(fn ($e) => self::ESTADOS[$e] ?? "Estado {$e}")->values(),
                'data' => $porEstado->values()->map(fn ($v) => (int) $v),
            ],
        ];

        $this->dispatch('actas-actualizadas', charts: $charts);

        $inspectoresConActas = DB::connection('mysql_faltas')->table('fa_acta')->distinct()->pluck('inspector_id');
        $departamentosConActas = DB::connection('mysql_faltas')->table('fa_acta')->distinct()->pluck('dto_id');

        return view('livewire.estadisticas-actas', [
            'totales' => $totales,
            'camaras' => $camaras,
            'lugaresCamaras' => $lugaresCamaras,
            'actas' => $actas,
            'charts' => $charts,
            'inspectores' => Inspector::whereIn('id', $inspectoresConActas)->orderBy('nombre')->get(['id', 'nombre']),
            'departamentos' => Departamento::whereIn('id', $departamentosConActas)->orderBy('nombre')->get(['id', 'nombre']),
            'estados' => self::ESTADOS,
        ]);
    }

    private function agrupacionTemporal(): string
    {
        if (! $this->filterFechaDesde || ! $this->filterFechaHasta) {
            return 'anio';
        }

        $dias = Carbon::parse($this->filterFechaDesde)->diffInDays(Carbon::parse($this->filterFechaHasta));

        return match (true) {
            $dias <= 62 => 'dia',
            $dias <= 731 => 'mes',
            default => 'anio',
        };
    }

    private function etiquetaPeriodo(?string $periodo, string $agrupacion): string
    {
        if (! $periodo) {
            return '-';
        }

        return match ($agrupacion) {
            'dia' => Carbon::parse($periodo)->format('d/m'),
            'mes' => Carbon::createFromFormat('Y-m-d', $periodo.'-01')->locale('es')->translatedFormat('M Y'),
            default => $periodo,
        };
    }
}
