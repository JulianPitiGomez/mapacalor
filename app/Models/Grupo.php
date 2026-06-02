<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Grupo extends Model
{
    protected $fillable = [
        'nombre',
        'departamento_id',
        'inspector_encargado_id',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function encargado(): BelongsTo
    {
        return $this->belongsTo(Inspector::class, 'inspector_encargado_id');
    }

    public function inspectores(): BelongsToMany
    {
        return $this->belongsToMany(Inspector::class, 'grupo_inspector', 'grupo_id', 'inspector_id')
            ->withTimestamps();
    }
}
