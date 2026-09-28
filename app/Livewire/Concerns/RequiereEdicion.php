<?php

namespace App\Livewire\Concerns;

trait RequiereEdicion
{
    /**
     * Corta la acción si el usuario es visualizador (solo lectura).
     * Llamar al principio de todo método Livewire que guarde o borre datos.
     */
    protected function autorizarEdicion(): void
    {
        abort_unless(auth()->user()?->puedeEditar(), 403);
    }
}
