<?php

namespace App\Livewire;

use App\Models\Grupo;
use App\Models\Inspector;
use App\Models\Departamento;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class GrupoForm extends Component
{
    public $grupoId = null;
    public $isEdit = false;

    public $nombre;
    public $departamento_id;
    public $inspector_encargado_id;
    public $inspectores_ids = [];

    public $departamentos = [];
    public $inspectores = [];

    public function mount($grupoId = null)
    {
        $this->departamentos = Departamento::where('marca', 1)->orderBy('nombre')->get();
        $this->inspectores = collect([]);

        if ($grupoId) {
            $this->grupoId = $grupoId;
            $this->isEdit = true;
            $grupo = Grupo::findOrFail($grupoId);
            $this->loadGrupo($grupo);
        }
    }

    private function loadGrupo($grupo)
    {
        $this->nombre = $grupo->nombre;
        $this->departamento_id = $grupo->departamento_id;
        $this->inspector_encargado_id = $grupo->inspector_encargado_id;

        // Cargar inspectores miembros desde la pivot
        $this->inspectores_ids = DB::table('grupo_inspector')
            ->where('grupo_id', $grupo->id)
            ->pluck('inspector_id')
            ->toArray();

        // Cargar inspectores del departamento
        if ($this->departamento_id) {
            $this->inspectores = Inspector::where('dto_id', $this->departamento_id)
                ->where('inhabilitado', false)
                ->where('dni', '>', 0)
                ->orderBy('nombre')
                ->get();
        }
    }

    public function updatedDepartamentoId()
    {
        if ($this->departamento_id) {
            $this->inspectores = Inspector::where('dto_id', $this->departamento_id)
                ->where('inhabilitado', false)
                ->where('dni', '>', 0)
                ->orderBy('nombre')
                ->get();

            $inspectoresDelDepartamento = $this->inspectores->pluck('id')->toArray();

            if ($this->inspector_encargado_id && !in_array($this->inspector_encargado_id, $inspectoresDelDepartamento)) {
                $this->inspector_encargado_id = null;
            }

            $this->inspectores_ids = array_values(array_intersect($this->inspectores_ids, $inspectoresDelDepartamento));
        } else {
            $this->inspectores = collect([]);
            $this->inspector_encargado_id = null;
            $this->inspectores_ids = [];
        }
    }

    public function updatedInspectorEncargadoId()
    {
        // Auto-incluir al encargado como miembro del grupo
        if ($this->inspector_encargado_id) {
            $encargadoId = (int) $this->inspector_encargado_id;
            if (!in_array($encargadoId, array_map('intval', $this->inspectores_ids))) {
                $this->inspectores_ids[] = (string) $encargadoId;
            }
        }
    }

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'departamento_id' => 'required|integer',
            'inspector_encargado_id' => 'required|integer',
            'inspectores_ids' => 'required|array|min:1',
            'inspectores_ids.*' => 'integer',
        ], [
            'nombre.required' => 'El nombre del grupo es obligatorio.',
            'departamento_id.required' => 'Debe seleccionar un departamento.',
            'inspector_encargado_id.required' => 'Debe seleccionar un encargado.',
            'inspectores_ids.required' => 'Debe seleccionar al menos un inspector.',
            'inspectores_ids.min' => 'Debe seleccionar al menos un inspector.',
        ]);

        // Asegurar que el encargado esté en los miembros
        $encargadoId = (int) $this->inspector_encargado_id;
        $inspectoresIds = array_map('intval', $this->inspectores_ids);
        if (!in_array($encargadoId, $inspectoresIds)) {
            $inspectoresIds[] = $encargadoId;
        }

        // Validar que ningún inspector pertenezca a otro grupo
        $query = DB::table('grupo_inspector')
            ->whereIn('inspector_id', $inspectoresIds);

        if ($this->isEdit) {
            $query->where('grupo_id', '!=', $this->grupoId);
        }

        $inspectoresEnOtroGrupo = $query->pluck('inspector_id')->toArray();

        if (!empty($inspectoresEnOtroGrupo)) {
            $nombres = Inspector::whereIn('id', $inspectoresEnOtroGrupo)->pluck('nombre')->implode(', ');
            $this->addError('inspectores_ids', "Los siguientes inspectores ya pertenecen a otro grupo: {$nombres}");
            return;
        }

        try {
            $data = [
                'nombre' => $this->nombre,
                'departamento_id' => $this->departamento_id,
                'inspector_encargado_id' => $this->inspector_encargado_id,
            ];

            if ($this->isEdit) {
                $grupo = Grupo::findOrFail($this->grupoId);
                $grupo->update($data);
                session()->flash('message', 'Grupo actualizado exitosamente.');
            } else {
                $grupo = Grupo::create($data);
                session()->flash('message', 'Grupo creado exitosamente.');
            }

            // Sincronizar inspectores miembros
            DB::table('grupo_inspector')->where('grupo_id', $grupo->id)->delete();

            $now = now();
            $pivotData = collect($inspectoresIds)->map(fn ($inspectorId) => [
                'grupo_id' => $grupo->id,
                'inspector_id' => $inspectorId,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

            if (!empty($pivotData)) {
                DB::table('grupo_inspector')->insert($pivotData);
            }

            return $this->redirect(route('grupos.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el grupo: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        return view('livewire.grupo-form');
    }
}
