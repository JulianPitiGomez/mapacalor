<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function subcategorias(): HasMany
    {
        return $this->hasMany(Subcategoria::class);
    }

    public function tiposInvolucrados(): HasMany
    {
        return $this->hasMany(TipoInvolucrado::class);
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function acciones(): HasMany
    {
        return $this->hasMany(Accion::class);
    }

    public function desenlaces(): HasMany
    {
        return $this->hasMany(Desenlace::class);
    }

    public function hechos(): HasMany
    {
        return $this->hasMany(Hecho::class);
    }
}
