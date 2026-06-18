<?php

namespace App\Livewire;

use App\Models\Grupo;
use App\Models\Departamento;
use App\Models\Inspector;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class GruposList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterDepartamento = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterDepartamento' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDepartamento()
    {
        $this->resetPage();
    }

    public function deleteGrupo($grupoId)
    {
        $grupo = Grupo::findOrFail($grupoId);
        DB::table('grupo_inspector')->where('grupo_id', $grupoId)->delete();
        $grupo->delete();

        $this->dispatch('toast', message: 'Grupo eliminado exitosamente.', type: 'success');
    }

    public function render()
    {
        $query = Grupo::query();

        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        if ($this->filterDepartamento) {
            $query->where('departamento_id', $this->filterDepartamento);
        }

        $grupos = $query->orderBy('nombre')->paginate(15);

        // Cargar relaciones manualmente (cross-database)
        $departamentoIds = $grupos->pluck('departamento_id')->unique()->filter();
        $encargadoIds = $grupos->pluck('inspector_encargado_id')->unique()->filter();

        $departamentosMap = collect();
        $inspectoresMap = collect();

        if ($departamentoIds->isNotEmpty()) {
            $departamentosMap = Departamento::whereIn('id', $departamentoIds)->get()->keyBy('id');
        }

        if ($encargadoIds->isNotEmpty()) {
            $inspectoresMap = Inspector::whereIn('id', $encargadoIds)->get()->keyBy('id');
        }

        // Contar miembros por grupo
        $miembrosCounts = DB::table('grupo_inspector')
            ->whereIn('grupo_id', $grupos->pluck('id'))
            ->selectRaw('grupo_id, count(*) as total')
            ->groupBy('grupo_id')
            ->pluck('total', 'grupo_id');

        foreach ($grupos as $grupo) {
            $grupo->setRelation('departamento', $departamentosMap->get($grupo->departamento_id));
            $grupo->setRelation('encargado', $inspectoresMap->get($grupo->inspector_encargado_id));
            $grupo->miembros_count = $miembrosCounts->get($grupo->id, 0);
        }

        $departamentos = Departamento::where('marca', 1)->orderBy('nombre')->get();

        return view('livewire.grupos-list', [
            'grupos' => $grupos,
            'departamentos' => $departamentos,
        ]);
    }
}
