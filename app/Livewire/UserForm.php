<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rules\Password;

class UserForm extends Component
{
    public $userId = null;
    public $isEdit = false;

    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $es_supervisor = false;

    public function mount($userId = null)
    {
        if ($userId) {
            $this->userId = $userId;
            $this->isEdit = true;
            $user = User::findOrFail($userId);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->es_supervisor = (bool) $user->es_supervisor;
        }
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email' . ($this->isEdit ? ',' . $this->userId : ''),
            'es_supervisor' => 'boolean',
        ];

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
        ];

        $this->validate($rules, $messages);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'es_supervisor' => $this->es_supervisor,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->isEdit) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            session()->flash('message', 'Usuario actualizado exitosamente.');
        } else {
            User::create($data);
            session()->flash('message', 'Usuario creado exitosamente.');
        }

        return $this->redirect(route('usuarios.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.user-form');
    }
}
