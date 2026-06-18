<?php

namespace App\Livewire;

use App\Models\Operativo;
use App\Models\Inspector;
use App\Models\Departamento;
use App\Models\Grupo;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OperativoForm extends Component
{
    public $operativoId = null;
    public $isEdit = false;

    // Campos del formulario
    public $descripcion;
    public $lugar;
    public $latitud;
    public $longitud;
    public $fecha;
    public $hora_desde;
    public $hora_hasta;
    public $estado = 'planificado';
    public $departamento_id;
    public $inspector_referente_id;
    public $observaciones;
    public $inspectores_ids = [];

    // Campos de solo lectura (cargados por el encargado en otro modulo)
    public $hora_apertura_real;
    public $hora_cierre_real;
    public $acompanamiento_policial;
    public $inspectores_participantes = []; // datos pivot con estado y observacion

    // Datos para los selects
    public $departamentos = [];
    public $inspectores = [];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount($operativoId = null)
    {
        $this->departamentos = Departamento::where('marca', 1)->orderBy('nombre')->get();
        $this->inspectores = collect([]); // Inicialmente vacío hasta que se seleccione departamento

        if ($operativoId) {
            $this->operativoId = $operativoId;
            $this->isEdit = true;
            $operativo = Operativo::findOrFail($operativoId);
            $this->loadOperativo($operativo);
        } else {
            $this->isEdit = false;
            $this->fecha = date('Y-m-d');
            $this->hora_desde = '08:00';
            $this->hora_hasta = '14:00';
        }
    }

    private function loadOperativo($operativo)
    {
        $this->descripcion = $operativo->descripcion;
        $this->lugar = $operativo->lugar;
        $this->latitud = $operativo->latitud;
        $this->longitud = $operativo->longitud;
        $this->fecha = $operativo->fecha->format('Y-m-d');
        $this->hora_desde = \Carbon\Carbon::parse($operativo->hora_desde)->format('H:i');
        $this->hora_hasta = \Carbon\Carbon::parse($operativo->hora_hasta)->format('H:i');
        $this->estado = $operativo->estado;
        $this->departamento_id = $operativo->departamento_id;
        $this->inspector_referente_id = $operativo->inspector_referente_id;
        $this->observaciones = $operativo->observaciones;
        // Cargar inspectores desde la pivot (conexión mysql) + datos del inspector (conexión mysql_faltas)
        $pivotRows = DB::connection('mysql')->table('operativo_inspector')
            ->where('operativo_id', $operativo->id)
            ->get();

        $this->inspectores_ids = $pivotRows->pluck('inspector_id')->toArray();

        $this->hora_apertura_real = $operativo->hora_apertura_real ? \Carbon\Carbon::parse($operativo->hora_apertura_real)->format('H:i') : null;
        $this->hora_cierre_real = $operativo->hora_cierre_real ? \Carbon\Carbon::parse($operativo->hora_cierre_real)->format('H:i') : null;
        $this->acompanamiento_policial = $operativo->acompanamiento_policial;

        // Cargar datos pivot de inspectores (estado y observacion)
        $inspectorIds = $pivotRows->pluck('inspector_id')->filter();
        $inspectoresMap = $inspectorIds->isNotEmpty()
            ? Inspector::whereIn('id', $inspectorIds)->get()->keyBy('id')
            : collect();

        $this->inspectores_participantes = $pivotRows->map(function ($pivot) use ($inspectoresMap) {
            $inspector = $inspectoresMap->get($pivot->inspector_id);
            return [
                'id' => $pivot->inspector_id,
                'nombre' => $inspector->nombre ?? '-',
                'dni' => $inspector->dni ?? '-',
                'estado' => $pivot->estado,
                'observacion' => $pivot->observacion,
            ];
        })->toArray();

        // Cargar inspectores del departamento seleccionado
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
        // Filtrar inspectores por departamento si se selecciona uno
        if ($this->departamento_id) {
            $this->inspectores = Inspector::where('dto_id', $this->departamento_id)
                ->where('inhabilitado', false)
                ->where('dni', '>', 0)
                ->orderBy('nombre')
                ->get();

            // Limpiar selecciones si los inspectores ya no pertenecen al departamento
            $inspectoresDelDepartamento = $this->inspectores->pluck('id')->toArray();

            if ($this->inspector_referente_id && !in_array($this->inspector_referente_id, $inspectoresDelDepartamento)) {
                $this->inspector_referente_id = null;
            }

            $this->inspectores_ids = array_intersect($this->inspectores_ids, $inspectoresDelDepartamento);
        } else {
            $this->inspectores = collect([]);
            $this->inspector_referente_id = null;
            $this->inspectores_ids = [];
        }
    }

    public function updatedInspectorReferenteId()
    {
        if ($this->inspector_referente_id) {
            // Buscar si el inspector referente es encargado de algún grupo en este departamento
            $grupo = Grupo::where('inspector_encargado_id', $this->inspector_referente_id)
                ->where('departamento_id', $this->departamento_id)
                ->first();

            if ($grupo) {
                // Obtener los IDs de los miembros del grupo
                $miembrosIds = DB::table('grupo_inspector')
                    ->where('grupo_id', $grupo->id)
                    ->pluck('inspector_id')
                    ->map(fn ($id) => (string) $id)
                    ->toArray();

                // Auto-tildar los miembros del grupo
                $this->inspectores_ids = $miembrosIds;
            }
        }
    }

    public function save()
    {
        \Log::info('=== INICIANDO GUARDADO DE OPERATIVO ===');
        \Log::info('isEdit: ' . ($this->isEdit ? 'true' : 'false'));
        \Log::info('operativoId: ' . ($this->operativoId ?? 'null'));

        try {
            $validated = $this->validate([
                'descripcion' => 'required|string|max:255',
                'lugar' => 'required|string|max:255',
                'latitud' => 'nullable|numeric|between:-90,90',
                'longitud' => 'nullable|numeric|between:-180,180',
                'fecha' => 'required|date',
                'hora_desde' => 'required|date_format:H:i',
                'hora_hasta' => 'required|date_format:H:i|after:hora_desde',
                'estado' => 'required|in:planificado,en_curso,finalizado,cancelado',
                'departamento_id' => 'required|integer',
                'inspector_referente_id' => 'required|integer',
                'observaciones' => 'nullable|string',
                'inspectores_ids' => 'array',
                'inspectores_ids.*' => 'integer',
            ], [
                'descripcion.required' => 'La descripción es obligatoria.',
                'lugar.required' => 'El lugar es obligatorio.',
                'fecha.required' => 'La fecha es obligatoria.',
                'hora_desde.required' => 'La hora de inicio es obligatoria.',
                'hora_hasta.required' => 'La hora de fin es obligatoria.',
                'hora_hasta.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
                'departamento_id.required' => 'Debe seleccionar un departamento.',
                'inspector_referente_id.required' => 'Debe seleccionar un inspector referente.',
            ]);

            \Log::info('Validación exitosa');
        } catch (\Exception $e) {
            \Log::error('Error en validación: ' . $e->getMessage());
            throw $e;
        }

        $data = [
            'descripcion' => $this->descripcion,
            'lugar' => $this->lugar,
            'latitud' => $this->latitud ?: null,
            'longitud' => $this->longitud ?: null,
            'fecha' => $this->fecha,
            'hora_desde' => $this->hora_desde,
            'hora_hasta' => $this->hora_hasta,
            'estado' => $this->estado,
            'departamento_id' => $this->departamento_id,
            'inspector_referente_id' => $this->inspector_referente_id,
            'observaciones' => $this->observaciones,
            'user_id' => Auth::id(),
        ];

        try {
            if ($this->isEdit) {
                \Log::info('Actualizando operativo ID: ' . $this->operativoId);
                $operativo = Operativo::findOrFail($this->operativoId);
                $operativo->update($data);
                \Log::info('Operativo actualizado exitosamente');
                $this->dispatch('toast', message: 'Operativo actualizado exitosamente.', type: 'success');
            } else {
                \Log::info('Creando nuevo operativo');
                $operativo = Operativo::create($data);
                \Log::info('Operativo creado con ID: ' . $operativo->id);
                $this->dispatch('toast', message: 'Operativo registrado exitosamente.', type: 'success');
            }

            // Sincronizar inspectores participantes (manual, evita cross-database)
            $pivotTable = DB::connection('mysql')->table('operativo_inspector');
            $pivotTable->where('operativo_id', $operativo->id)->delete();

            $now = now();
            $pivotData = collect($this->inspectores_ids)->map(fn ($inspectorId) => [
                'operativo_id' => $operativo->id,
                'inspector_id' => $inspectorId,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

            if (!empty($pivotData)) {
                DB::connection('mysql')->table('operativo_inspector')->insert($pivotData);
            }

            \Log::info('Redirigiendo a index');
            return $this->redirect(route('operativos.index'), navigate: true);
        } catch (\Exception $e) {
            \Log::error('Error al guardar operativo: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            $this->dispatch('toast', message: 'Error al guardar el operativo: ' . $e->getMessage(), type: 'error');
            return null;
        }
    }

    public function render()
    {
        return view('livewire.operativo-form');
    }
}
