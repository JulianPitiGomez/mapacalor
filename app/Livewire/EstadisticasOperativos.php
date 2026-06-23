<?php

namespace App\Livewire;

use App\Models\Operativo;
use App\Models\Departamento;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class EstadisticasOperativos extends Component
{
    public string $filterFechaDesde = '';
    public string $filterFechaHasta = '';
    public string $filterQuick = '';
    public bool $showMapa = false;

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
                $this->filterFechaHasta = now()->format('Y-m-d');
                break;
            case 'semana':
                $this->filterFechaDesde = now()->startOfWeek()->format('Y-m-d');
                $this->filterFechaHasta = now()->format('Y-m-d');
                break;
            case 'mes':
                $this->filterFechaDesde = now()->startOfMonth()->format('Y-m-d');
                $this->filterFechaHasta = now()->format('Y-m-d');
                break;
            case 'todos':
                $this->filterFechaDesde = '';
                $this->filterFechaHasta = '';
                break;
        }
    }

    public function toggleMapa(): void
    {
        $this->showMapa = !$this->showMapa;
    }

    public function render()
    {
        $departamentos = Departamento::where('marca', 1)->orderBy('nombre')->get();

        // Operativos filtrados por fecha
        $operativosQuery = Operativo::query();
        if ($this->filterFechaDesde) {
            $operativosQuery->whereDate('fecha', '>=', $this->filterFechaDesde);
        }
        if ($this->filterFechaHasta) {
            $operativosQuery->whereDate('fecha', '<=', $this->filterFechaHasta);
        }

        $operativosFiltrados = $operativosQuery->get(['id', 'descripcion', 'fecha', 'lugar', 'departamento_id']);
        $todosLosIds = $operativosFiltrados->pluck('id');
        $totalOperativos = $todosLosIds->count();

        $statsActas = (object)[
            'total_actas'       => 0,
            'total_secuestros'  => 0,
            'total_decomisos'   => 0,
            'total_clausuras'   => 0,
            'total_retiene_lic' => 0,
        ];
        $totalRegistros    = 0;
        $actasPorOperativo = collect();
        $actasDetalle      = collect();

        if ($todosLosIds->isNotEmpty()) {
            $raw = DB::connection('mysql_faltas')
                ->table('fa_acta')
                ->whereIn('operativo_id', $todosLosIds)
                ->selectRaw('COUNT(*) as total_actas, SUM(secuestro) as total_secuestros, SUM(decomiso) as total_decomisos, SUM(clausura) as total_clausuras, SUM(retiene_lic) as total_retiene_lic')
                ->first();
            if ($raw) $statsActas = $raw;

            $totalRegistros = DB::connection('mysql_faltas')
                ->table('fa_registro_control')
                ->whereIn('operativo_id', $todosLosIds)
                ->count();

            // JOIN cross-database separado en PHP: mysql_faltas no puede joinear con mysql
            $actasPorOperativo = DB::connection('mysql_faltas')
                ->table('fa_acta')
                ->whereIn('operativo_id', $todosLosIds)
                ->selectRaw('operativo_id, COUNT(id) as total_actas, SUM(secuestro) as secuestros, SUM(decomiso) as decomisos, SUM(clausura) as clausuras, SUM(retiene_lic) as retiene_lic')
                ->groupBy('operativo_id')
                ->orderByDesc('total_actas')
                ->get()
                ->map(function ($row) use ($operativosFiltrados, $departamentos) {
                    $op = $operativosFiltrados->firstWhere('id', $row->operativo_id);
                    $row->descripcion        = $op?->descripcion ?? '-';
                    $row->fecha              = $op?->fecha;
                    $row->lugar              = $op?->lugar ?? '-';
                    $row->departamento_nombre = $departamentos->firstWhere('id', $op?->departamento_id)?->nombre ?? '-';
                    return $row;
                });

            $actasDetalle = DB::connection('mysql_faltas')
                ->table('fa_acta as a')
                ->leftJoin('fa_inspector as i', 'i.id', '=', 'a.inspector_id')
                ->leftJoin('fa_tiporodado as t', 't.id', '=', 'a.tipo_id')
                ->leftJoin('fa_marca as m', 'm.id', '=', 'a.marca_id')
                ->leftJoin('fa_acta_motivo as am', 'am.acta_id', '=', 'a.id')
                ->leftJoin('fa_motivo as mo', 'mo.id', '=', 'am.motivo_id')
                ->whereIn('a.operativo_id', $todosLosIds)
                ->select(
                    'a.id', 'a.actanro', 'a.operativo_id', 'a.fecha', 'a.hora',
                    'a.nombreinf', 'a.dni', 'a.direcinf',
                    'a.dominio', 'a.licencia', 'a.modelo',
                    'a.secuestro', 'a.decomiso', 'a.clausura', 'a.retiene_lic',
                    'a.obs', 'a.grad_alcohol',
                    DB::raw('i.nombre as inspector_nombre'),
                    DB::raw('t.nombre as tipo_rodado'),
                    DB::raw('m.nombre as marca_vehiculo'),
                    DB::raw('GROUP_CONCAT(DISTINCT mo.nombre ORDER BY mo.nombre SEPARATOR " | ") as motivos')
                )
                ->groupBy(
                    'a.id', 'a.actanro', 'a.operativo_id', 'a.fecha', 'a.hora',
                    'a.nombreinf', 'a.dni', 'a.direcinf',
                    'a.dominio', 'a.licencia', 'a.modelo',
                    'a.secuestro', 'a.decomiso', 'a.clausura', 'a.retiene_lic',
                    'a.obs', 'a.grad_alcohol',
                    'i.nombre', 't.nombre', 'm.nombre'
                )
                ->orderBy('a.fecha')
                ->orderBy('a.hora')
                ->get()
                ->map(function ($acta) {
                    $nro = str_pad($acta->actanro, 10, '0', STR_PAD_LEFT);
                    $fotosPath = rtrim(config('services.actas.fotos_path') ?? public_path('fotos'), '/\\');
                    $fotosUrl  = rtrim(config('services.actas.fotos_url')  ?? url('fotos'), '/');
                    $fotos = [];
                    for ($i = 1; $i <= 5; $i++) {
                        $nombre = 'fot-' . $nro . '-' . str_pad($i, 3, '0', STR_PAD_LEFT) . '.jpg';
                        if (@file_exists($fotosPath . DIRECTORY_SEPARATOR . $nombre)) {
                            $fotos[] = $fotosUrl . '/' . $nombre;
                        }
                    }
                    $acta->fotos = $fotos;
                    return $acta;
                })
                ->groupBy('operativo_id');
        }

        // Operativos geolocalizados — igual que el mapa de operativos: Eloquent + queries separadas
        $opGeoQuery = Operativo::whereNotNull('latitud')->whereNotNull('longitud');
        if ($this->filterFechaDesde) {
            $opGeoQuery->whereDate('fecha', '>=', $this->filterFechaDesde);
        }
        if ($this->filterFechaHasta) {
            $opGeoQuery->whereDate('fecha', '<=', $this->filterFechaHasta);
        }
        $operativosGeo = $opGeoQuery->get(['id', 'latitud', 'longitud', 'descripcion', 'lugar', 'fecha', 'estado', 'departamento_id']);

        if ($operativosGeo->isNotEmpty()) {
            $geoIds = $operativosGeo->pluck('id');

            $actasCounts = DB::connection('mysql_faltas')
                ->table('fa_acta')
                ->whereIn('operativo_id', $geoIds)
                ->selectRaw('operativo_id, COUNT(*) as cnt')
                ->groupBy('operativo_id')
                ->get()->keyBy('operativo_id');

            $registrosCounts = DB::connection('mysql_faltas')
                ->table('fa_registro_control')
                ->whereIn('operativo_id', $geoIds)
                ->selectRaw('operativo_id, COUNT(*) as cnt')
                ->groupBy('operativo_id')
                ->get()->keyBy('operativo_id');

            $geoDeptIds     = $operativosGeo->pluck('departamento_id')->unique()->filter();
            $departamentosGeo = $geoDeptIds->isNotEmpty()
                ? Departamento::whereIn('id', $geoDeptIds)->get()->keyBy('id')
                : collect();

            $mapaPuntos = $operativosGeo->map(function ($op) use ($actasCounts, $registrosCounts, $departamentosGeo) {
                return [
                    'lat'             => (float) $op->latitud,
                    'lng'             => (float) $op->longitud,
                    'id'              => $op->id,
                    'descripcion'     => $op->descripcion,
                    'lugar'           => $op->lugar,
                    'fecha'           => $op->fecha->format('d/m/Y'),
                    'estado'          => $op->estado,
                    'departamento'    => $departamentosGeo->get($op->departamento_id)?->nombre ?? '-',
                    'total_actas'     => (int) ($actasCounts->get($op->id)?->cnt ?? 0),
                    'total_registros' => (int) ($registrosCounts->get($op->id)?->cnt ?? 0),
                ];
            })->values();
        } else {
            $mapaPuntos = collect();
        }

        $totalActasMapa     = $mapaPuntos->sum('total_actas');
        $totalRegistrosMapa = $mapaPuntos->sum('total_registros');

        return view('livewire.estadisticas-operativos', [
            'totalOperativos'    => $totalOperativos,
            'statsActas'         => $statsActas,
            'totalRegistros'     => $totalRegistros,
            'actasPorOperativo'  => $actasPorOperativo,
            'actasDetalle'       => $actasDetalle,
            'totalActasMapa'     => $totalActasMapa,
            'totalRegistrosMapa' => $totalRegistrosMapa,
            'mapaPuntos'         => $mapaPuntos,
        ]);
    }
}
