<?php

namespace App\Livewire;

use App\Models\Hecho;
use App\Models\Categoria;
use App\Models\Barrio;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class HechosList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategoria = '';
    public $filterBarrio = '';
    public $filterFechaDesde = '';
    public $filterFechaHasta = '';

    // Filtros de etiquetas dinámicas
    public $etiquetasCategoria = [];
    public $filtrosEtiquetas = [];
    public $mapaEtiquetas = []; // clave_slug => nombre_original

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategoria' => ['except' => ''],
        'filterBarrio' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategoria()
    {
        $this->resetPage();
    }

    public function updatedFilterCategoria($value)
    {
        // Cargar etiquetas de la categoría seleccionada
        if ($value) {
            $categoria = Categoria::find($value);
            if ($categoria && $categoria->etiquetas) {
                $this->etiquetasCategoria = $categoria->etiquetas;
                // Inicializar filtros vacíos para cada etiqueta usando clave slug
                $this->filtrosEtiquetas = [];
                $this->mapaEtiquetas = [];
                foreach ($this->etiquetasCategoria as $etiqueta) {
                    $nombre = is_array($etiqueta) ? ($etiqueta['nombre'] ?? '') : $etiqueta;
                    $clave = Str::slug($nombre, '_');
                    $this->filtrosEtiquetas[$clave] = '';
                    $this->mapaEtiquetas[$clave] = $nombre;
                }
            } else {
                $this->etiquetasCategoria = [];
                $this->filtrosEtiquetas = [];
                $this->mapaEtiquetas = [];
            }
        } else {
            $this->etiquetasCategoria = [];
            $this->filtrosEtiquetas = [];
            $this->mapaEtiquetas = [];
        }
    }

    public function updatingFilterBarrio()
    {
        $this->resetPage();
    }

    public function updatingFiltrosEtiquetas()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->filterCategoria = '';
        $this->filterBarrio = '';
        $this->filterFechaDesde = '';
        $this->filterFechaHasta = '';
        $this->etiquetasCategoria = [];
        $this->filtrosEtiquetas = [];
        $this->mapaEtiquetas = [];
        $this->resetPage();
    }

    public function deleteHecho($hechoId)
    {
        $hecho = Hecho::findOrFail($hechoId);
        $hecho->delete();

        session()->flash('message', 'Hecho eliminado exitosamente.');
    }

    public function render()
    {
        $query = Hecho::with(['categoria', 'subcategoria', 'barrio', 'user']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhere('observaciones', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterCategoria) {
            $query->where('categoria_id', $this->filterCategoria);
        }

        if ($this->filterBarrio) {
            $query->where('barrio_id', $this->filterBarrio);
        }

        if ($this->filterFechaDesde) {
            $query->whereDate('fecha_hecho', '>=', $this->filterFechaDesde);
        }

        if ($this->filterFechaHasta) {
            $query->whereDate('fecha_hecho', '<=', $this->filterFechaHasta);
        }

        // Filtros de etiquetas dinámicas
        if (!empty($this->filtrosEtiquetas) && !empty($this->mapaEtiquetas)) {
            foreach ($this->filtrosEtiquetas as $clave => $valor) {
                // Asegurarse de que el valor sea string
                if (is_array($valor)) {
                    continue;
                }
                $valorLimpio = trim((string) $valor);
                if (!empty($valorLimpio)) {
                    // Obtener el nombre original de la etiqueta
                    $nombreOriginal = $this->mapaEtiquetas[$clave] ?? $clave;
                    // Buscar en observaciones el patrón "Etiqueta -> valor"
                    $query->where('observaciones', 'like', '%' . $nombreOriginal . ' -> %' . $valorLimpio . '%');
                }
            }
        }

        $hechos = $query->orderBy('fecha_hecho', 'desc')->paginate(15);
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
        $barrios = Barrio::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.hechos-list', [
            'hechos' => $hechos,
            'categorias' => $categorias,
            'barrios' => $barrios
        ]);
    }
}
