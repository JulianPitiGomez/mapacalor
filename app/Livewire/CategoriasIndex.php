<?php

namespace App\Livewire;

use App\Models\Categoria;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriasIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $categorias = Categoria::withCount(['subcategorias', 'tiposInvolucrados', 'horarios', 'acciones', 'desenlaces'])
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.categorias-index', compact('categorias'));
    }
}
