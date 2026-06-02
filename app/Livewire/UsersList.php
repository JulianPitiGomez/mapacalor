<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersList extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteUser($userId)
    {
        if ($userId == auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propio usuario.');
            return;
        }

        $user = User::findOrFail($userId);
        $user->delete();

        session()->flash('message', 'Usuario eliminado exitosamente.');
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(15);

        return view('livewire.users-list', [
            'users' => $users,
        ]);
    }
}
