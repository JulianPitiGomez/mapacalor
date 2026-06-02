<?php

namespace App\Livewire;

use App\Models\Operativo;
use App\Models\Departamento;
use App\Models\Inspector;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class OperativosList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterDepartamento = '';
    public $filterEstado = '';
    public $filterFechaDesde = '';
    public $filterFechaHasta = '';
    public $showMap = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterDepartamento' => ['except' => ''],
        'filterEstado' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDepartamento()
    {
        $this->resetPage();
    }

    public function updatingFilterEstado()
    {
        $this->resetPage();
    }

    public function toggleMap()
    {
        $this->showMap = !$this->showMap;
    }

    public function deleteOperativo($operativoId)
    {
        $operativo = Operativo::findOrFail($operativoId);
        DB::connection('mysql')->table('operativo_inspector')->where('operativo_id', $operativoId)->delete();
        $operativo->delete();

        session()->flash('message', 'Operativo eliminado exitosamente.');
    }

    public function cambiarEstado($operativoId, $nuevoEstado)
    {
        $operativo = Operativo::findOrFail($operativoId);
        $operativo->update(['estado' => $nuevoEstado]);

        session()->flash('message', 'Estado del operativo actualizado.');
    }

    public function render()
    {
        $query = Operativo::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('lugar', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterDepartamento) {
            $query->where('departamento_id', $this->filterDepartamento);
        }

        if ($this->filterEstado) {
            $query->where('estado', $this->filterEstado);
        }

        if ($this->filterFechaDesde) {
            $query->whereDate('fecha', '>=', $this->filterFechaDesde);
        }

        if ($this->filterFechaHasta) {
            $query->whereDate('fecha', '<=', $this->filterFechaHasta);
        }

        $operativos = $query->orderBy('fecha', 'desc')->orderBy('hora_desde', 'desc')->paginate(15);

        // Cargar relaciones manualmente debido a cross-database
        $departamentoIds = $operativos->pluck('departamento_id')->unique()->filter();
        $inspectorReferenteIds = $operativos->pluck('inspector_referente_id')->unique()->filter();

        $departamentosMap = [];
        $inspectoresMap = [];

        if ($departamentoIds->isNotEmpty()) {
            $departamentosMap = Departamento::whereIn('id', $departamentoIds)->get()->keyBy('id');
        }

        if ($inspectorReferenteIds->isNotEmpty()) {
            $inspectoresMap = Inspector::whereIn('id', $inspectorReferenteIds)->get()->keyBy('id');
        }

        // Asignar relaciones manualmente
        foreach ($operativos as $operativo) {
            $operativo->setRelation('departamento', $departamentosMap->get($operativo->departamento_id));
            $operativo->setRelation('inspectorReferente', $inspectoresMap->get($operativo->inspector_referente_id));
        }

        $departamentos = Departamento::where('marca', 1)->orderBy('nombre')->get();

        // Obtener operativos para el mapa (con coordenadas) - siempre calcular
        $operativosParaMapa = $query->clone()
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get()
            ->map(function($op) use ($departamentosMap, $inspectoresMap) {
                return [
                    'id' => $op->id,
                    'descripcion' => $op->descripcion,
                    'lugar' => $op->lugar,
                    'latitud' => $op->latitud,
                    'longitud' => $op->longitud,
                    'fecha' => $op->fecha->format('d/m/Y'),
                    'hora_desde' => \Carbon\Carbon::parse($op->hora_desde)->format('H:i'),
                    'hora_hasta' => \Carbon\Carbon::parse($op->hora_hasta)->format('H:i'),
                    'estado' => $op->estado,
                    'estado_label' => $op->estado_label,
                    'departamento' => $departamentosMap->get($op->departamento_id)?->nombre ?? '-',
                    'inspector_referente' => $inspectoresMap->get($op->inspector_referente_id)?->nombre ?? '-',
                ];
            });

        return view('livewire.operativos-list', [
            'operativos' => $operativos,
            'departamentos' => $departamentos,
            'operativosParaMapa' => $operativosParaMapa,
        ]);
    }
}
