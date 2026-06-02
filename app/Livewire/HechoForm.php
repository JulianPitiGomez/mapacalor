<?php

namespace App\Livewire;

use App\Models\Hecho;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\TipoInvolucrado;
use App\Models\Horario;
use App\Models\Accion;
use App\Models\Desenlace;
use App\Models\Barrio;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HechoForm extends Component
{
    public $hechoId = null;
    public $isEdit = false;

    // Campos del formulario
    public $fecha_hecho;
    public $categoria_id;
    public $subcategoria_id;
    public $tipo_involucrado1_id;
    public $sexo_involucrado1 = 'Sin datos';
    public $edad_involucrado1;
    public $tipo_involucrado2_id;
    public $sexo_involucrado2 = 'Sin datos';
    public $edad_involucrado2;
    public $horario_id;
    public $accion_id;
    public $desenlace_id;
    public $latitud;
    public $longitud;
    public $barrio_id;
    public $observaciones;

    // Datos para los selects
    public $categorias = [];
    public $subcategorias = [];
    public $tiposInvolucrados = [];
    public $horarios = [];
    public $acciones = [];
    public $desenlaces = [];
    public $barrios = [];

    // Etiquetas dinámicas de la categoría
    public $etiquetasCategoria = [];
    public $valoresEtiquetas = [];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount($hechoId = null)
    {
        $this->categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
        $this->barrios = Barrio::where('activo', true)->orderBy('nombre')->get();

        // Inicializar colecciones vacías
        $this->subcategorias = collect([]);
        $this->tiposInvolucrados = collect([]);
        $this->horarios = collect([]);
        $this->etiquetasCategoria = [];
        $this->valoresEtiquetas = [];

        if ($hechoId) {
            $this->hechoId = $hechoId;
            $this->isEdit = true;
            $hecho = Hecho::findOrFail($hechoId);
            $this->loadHecho($hecho);
        } else {
            $this->isEdit = false;
            $this->fecha_hecho = date('Y-m-d');
        }
    }

    private function loadHecho($hecho)
    {
        $this->fecha_hecho = $hecho->fecha_hecho->format('Y-m-d');
        $this->categoria_id = $hecho->categoria_id;
        $this->subcategoria_id = $hecho->subcategoria_id;
        $this->tipo_involucrado1_id = $hecho->tipo_involucrado1_id;
        $this->sexo_involucrado1 = $hecho->sexo_involucrado1 ?? 'Sin datos';
        $this->edad_involucrado1 = $hecho->edad_involucrado1;
        $this->tipo_involucrado2_id = $hecho->tipo_involucrado2_id;
        $this->sexo_involucrado2 = $hecho->sexo_involucrado2 ?? 'Sin datos';
        $this->edad_involucrado2 = $hecho->edad_involucrado2;
        $this->horario_id = $hecho->horario_id;
        $this->accion_id = $hecho->accion_id;
        $this->desenlace_id = $hecho->desenlace_id;
        $this->latitud = $hecho->latitud;
        $this->longitud = $hecho->longitud;
        $this->barrio_id = $hecho->barrio_id;
        $this->observaciones = $hecho->observaciones;

        // Cargar relaciones sin resetear los valores
        if ($this->categoria_id) {
            $this->subcategorias = Subcategoria::where('categoria_id', $this->categoria_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->tiposInvolucrados = TipoInvolucrado::where('categoria_id', $this->categoria_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->horarios = Horario::where('categoria_id', $this->categoria_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->acciones = Accion::where('categoria_id', $this->categoria_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->desenlaces = Desenlace::where('categoria_id', $this->categoria_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            // Cargar etiquetas de la categoría
            $categoria = Categoria::find($this->categoria_id);
            if ($categoria && $categoria->etiquetas) {
                $this->etiquetasCategoria = $categoria->etiquetas;

                // Parsear valores de etiquetas desde observaciones
                $this->valoresEtiquetas = [];
                $mapaEtiquetas = []; // nombre => clave_slug

                foreach ($this->etiquetasCategoria as $etiqueta) {
                    $nombre = is_array($etiqueta) ? ($etiqueta['nombre'] ?? '') : $etiqueta;
                    $clave = Str::slug($nombre, '_');
                    $mapaEtiquetas[$nombre] = $clave;
                    $this->valoresEtiquetas[$clave] = '';
                }

                if ($hecho->observaciones) {
                    foreach ($mapaEtiquetas as $nombre => $clave) {
                        // Buscar patrón "Etiqueta -> valor"
                        if (preg_match('/^' . preg_quote($nombre, '/') . '\s*->\s*(.*)$/m', $hecho->observaciones, $matches)) {
                            $this->valoresEtiquetas[$clave] = trim($matches[1]);
                        }
                    }

                    // Extraer observaciones adicionales (texto que no es etiqueta)
                    $lineas = explode("\n", $hecho->observaciones);
                    $observacionesLimpias = [];
                    foreach ($lineas as $linea) {
                        $esEtiqueta = false;
                        foreach (array_keys($mapaEtiquetas) as $nombre) {
                            if (preg_match('/^' . preg_quote($nombre, '/') . '\s*->/', $linea)) {
                                $esEtiqueta = true;
                                break;
                            }
                        }
                        if (!$esEtiqueta && trim($linea) !== '') {
                            $observacionesLimpias[] = $linea;
                        }
                    }
                    $this->observaciones = implode("\n", $observacionesLimpias);
                }
            }
        }
    }

    public function updatedCategoriaId()
    {
        if ($this->categoria_id) {
            $this->subcategorias = Subcategoria::where('categoria_id', $this->categoria_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->tiposInvolucrados = TipoInvolucrado::where('categoria_id', $this->categoria_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->horarios = Horario::where('categoria_id', $this->categoria_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->acciones = Accion::where('categoria_id', $this->categoria_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->desenlaces = Desenlace::where('categoria_id', $this->categoria_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            // Cargar etiquetas de la categoría
            $categoria = Categoria::find($this->categoria_id);
            if ($categoria && $categoria->etiquetas) {
                $this->etiquetasCategoria = $categoria->etiquetas;
                // Inicializar valores vacíos para cada etiqueta usando clave slug
                $this->valoresEtiquetas = [];
                foreach ($this->etiquetasCategoria as $etiqueta) {
                    $nombre = is_array($etiqueta) ? ($etiqueta['nombre'] ?? '') : $etiqueta;
                    $clave = Str::slug($nombre, '_');
                    $this->valoresEtiquetas[$clave] = '';
                }
            } else {
                $this->etiquetasCategoria = [];
                $this->valoresEtiquetas = [];
            }

            // Resetear campos dependientes cuando el usuario cambia la categoría
            $this->subcategoria_id = null;
            $this->tipo_involucrado1_id = null;
            $this->tipo_involucrado2_id = null;
            $this->horario_id = null;
            $this->accion_id = null;
            $this->desenlace_id = null;
        } else {
            $this->subcategorias = [];
            $this->tiposInvolucrados = [];
            $this->horarios = [];
            $this->acciones = [];
            $this->desenlaces = [];
            $this->etiquetasCategoria = [];
            $this->valoresEtiquetas = [];
            $this->subcategoria_id = null;
            $this->tipo_involucrado1_id = null;
            $this->tipo_involucrado2_id = null;
            $this->horario_id = null;
            $this->accion_id = null;
            $this->desenlace_id = null;
        }
    }

    public function save()
    {
        \Log::info('=== INICIANDO GUARDADO DE HECHO ===');
        \Log::info('isEdit: ' . ($this->isEdit ? 'true' : 'false'));
        \Log::info('hechoId: ' . ($this->hechoId ?? 'null'));

        try {
            $validated = $this->validate([
                'fecha_hecho' => 'required|date',
                'categoria_id' => 'required|exists:categorias,id',
                'subcategoria_id' => 'nullable|exists:subcategorias,id',
                'tipo_involucrado1_id' => 'nullable|exists:tipos_involucrados,id',
                'sexo_involucrado1' => 'required|in:Varon,Mujer,Sin datos',
                'edad_involucrado1' => 'nullable|integer|min:0|max:120',
                'tipo_involucrado2_id' => 'nullable|exists:tipos_involucrados,id',
                'sexo_involucrado2' => 'required|in:Varon,Mujer,Sin datos',
                'edad_involucrado2' => 'nullable|integer|min:0|max:120',
                'horario_id' => 'nullable|exists:horarios,id',
                'accion_id' => 'nullable|exists:acciones,id',
                'desenlace_id' => 'nullable|exists:desenlaces,id',
                'latitud' => 'nullable|numeric|between:-90,90',
                'longitud' => 'nullable|numeric|between:-180,180',
                'barrio_id' => 'nullable|exists:barrios,id',
                'observaciones' => 'nullable|string',
            ]);

            \Log::info('Validación exitosa');
        } catch (\Exception $e) {
            \Log::error('Error en validación: ' . $e->getMessage());
            throw $e;
        }

        // Construir observaciones con etiquetas
        $observacionesFinales = '';

        // Agregar etiquetas con valores
        if (!empty($this->etiquetasCategoria) && !empty($this->valoresEtiquetas)) {
            $etiquetasTexto = [];
            foreach ($this->etiquetasCategoria as $etiqueta) {
                // Obtener nombre original y clave slug
                $nombre = is_array($etiqueta) ? ($etiqueta['nombre'] ?? '') : $etiqueta;
                $clave = Str::slug($nombre, '_');
                $valor = $this->valoresEtiquetas[$clave] ?? '';

                // Asegurarse de que el valor sea string
                if (is_array($valor)) {
                    continue;
                }
                $valorLimpio = trim((string) $valor);
                if (!empty($valorLimpio)) {
                    // Usar el nombre original (con espacios) para guardar en observaciones
                    $etiquetasTexto[] = $nombre . ' -> ' . $valorLimpio;
                }
            }
            if (!empty($etiquetasTexto)) {
                $observacionesFinales = implode("\n", $etiquetasTexto);
            }
        }

        // Agregar observaciones adicionales si existen
        $obsAdicionales = is_string($this->observaciones) ? trim($this->observaciones) : '';
        if (!empty($obsAdicionales)) {
            if (!empty($observacionesFinales)) {
                $observacionesFinales .= "\n" . $obsAdicionales;
            } else {
                $observacionesFinales = $obsAdicionales;
            }
        }

        $data = [
            'fecha_hecho' => $this->fecha_hecho,
            'categoria_id' => $this->categoria_id,
            'subcategoria_id' => $this->subcategoria_id ?: null,
            'tipo_involucrado1_id' => $this->tipo_involucrado1_id ?: null,
            'sexo_involucrado1' => $this->sexo_involucrado1,
            'edad_involucrado1' => $this->edad_involucrado1 ?: null,
            'tipo_involucrado2_id' => $this->tipo_involucrado2_id ?: null,
            'sexo_involucrado2' => $this->sexo_involucrado2,
            'edad_involucrado2' => $this->edad_involucrado2 ?: null,
            'horario_id' => $this->horario_id ?: null,
            'accion_id' => $this->accion_id ?: null,
            'desenlace_id' => $this->desenlace_id ?: null,
            'latitud' => $this->latitud ?: null,
            'longitud' => $this->longitud ?: null,
            'barrio_id' => $this->barrio_id ?: null,
            'observaciones' => $observacionesFinales ?: null,
            'user_id' => Auth::id(),
        ];

        try {
            if ($this->isEdit) {
                \Log::info('Actualizando hecho ID: ' . $this->hechoId);
                $hecho = Hecho::findOrFail($this->hechoId);
                \Log::info('Hecho encontrado, actualizando...');
                \Log::info('Datos a actualizar:', $data);
                $hecho->update($data);
                \Log::info('Hecho actualizado exitosamente');
                session()->flash('message', 'Hecho actualizado exitosamente.');
            } else {
                \Log::info('Creando nuevo hecho');
                \Log::info('Datos a crear:', $data);
                $hecho = Hecho::create($data);
                \Log::info('Hecho creado con ID: ' . $hecho->id);
                session()->flash('message', 'Hecho registrado exitosamente.');
            }

            \Log::info('Redirigiendo a index');
            return $this->redirect(route('hechos.index'), navigate: true);
        } catch (\Exception $e) {
            \Log::error('Error al guardar hecho: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Error al guardar el hecho: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        return view('livewire.hecho-form');
    }
}
