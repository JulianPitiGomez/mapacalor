<?php

namespace App\Livewire;

use App\Livewire\Concerns\RequiereEdicion;
use App\Models\User;
use Livewire\Component;

class UserForm extends Component
{
    use RequiereEdicion;

    public $userId = null;

    public $isEdit = false;

    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $rol = User::ROL_NORMAL;

    /** Slugs de las solapas habilitadas cuando el rol es visualizador. */
    public $solapas = [];

    public function mount($userId = null)
    {
        if ($userId) {
            $this->userId = $userId;
            $this->isEdit = true;
            $user = User::findOrFail($userId);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->rol = $user->rol ?? ($user->es_supervisor ? User::ROL_SUPERVISOR : User::ROL_NORMAL);
            $this->solapas = $user->solapas ?? [];
        }
    }

    public function updatedRol($value)
    {
        // Las solapas solo tienen sentido para un visualizador.
        if ($value !== User::ROL_VISUALIZADOR) {
            $this->solapas = [];
        }
    }

    public function save()
    {
        $this->autorizarEdicion();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email'.($this->isEdit ? ','.$this->userId : ''),
            'rol' => 'required|in:'.implode(',', [User::ROL_NORMAL, User::ROL_SUPERVISOR, User::ROL_VISUALIZADOR]),
            'solapas' => 'array',
            'solapas.*' => 'in:'.implode(',', array_keys(User::SOLAPAS)),
        ];

        if ($this->rol === User::ROL_VISUALIZADOR) {
            $rules['solapas'] = 'required|array|min:1';
        }

        if ($this->isEdit) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        } else {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $messages = [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser una dirección válida.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'rol.required' => 'El rol es obligatorio.',
            'rol.in' => 'El rol seleccionado no es válido.',
            'solapas.required' => 'Seleccioná al menos una solapa para el visualizador.',
            'solapas.min' => 'Seleccioná al menos una solapa para el visualizador.',
        ];

        $this->validate($rules, $messages);

        $esVisualizador = $this->rol === User::ROL_VISUALIZADOR;

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'rol' => $this->rol,
            // Se mantiene sincronizado por compatibilidad con el resto del sistema.
            'es_supervisor' => $this->rol === User::ROL_SUPERVISOR,
            'solapas' => $esVisualizador
                ? array_values(array_intersect(array_keys(User::SOLAPAS), $this->solapas))
                : null,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->isEdit) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            $this->dispatch('toast', message: 'Usuario actualizado exitosamente.', type: 'success');
        } else {
            User::create($data);
            $this->dispatch('toast', message: 'Usuario creado exitosamente.', type: 'success');
        }

        return $this->redirect(route('usuarios.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.user-form', [
            'solapasDisponibles' => User::SOLAPAS,
            'roles' => [
                User::ROL_NORMAL => 'Normal',
                User::ROL_SUPERVISOR => 'Supervisor',
                User::ROL_VISUALIZADOR => 'Visualizador',
            ],
            'rolVisualizador' => User::ROL_VISUALIZADOR,
            'rolSupervisor' => User::ROL_SUPERVISOR,
        ]);
    }
}
