<?php

namespace App\Livewire;

use App\Models\Hecho;
use App\Models\Categoria;
use App\Models\Barrio;
use Livewire\Component;
use Livewire\WithPagination;

class HechosList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategoria = '';
    public $filterBarrio = '';
    public $filterFechaDesde = '';
    public $filterFechaHasta = '';

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

    public function updatingFilterBarrio()
    {
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
