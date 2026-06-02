<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Grupo;

class Inspector extends Model
{
    protected $connection = 'mysql_faltas';
    protected $table = 'fa_inspector';

    protected $fillable = [
        'nombre',
        'dni',
        'dto_id',
    ];

    public $timestamps = false;

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'dto_id');
    }

    public function operativos(): BelongsToMany
    {
        return $this->belongsToMany(Operativo::class, 'munimer_mapacalor.operativo_inspector', 'inspector_id', 'operativo_id')
            ->withTimestamps();
    }

    public function operativosComoReferente()
    {
        return $this->hasMany(Operativo::class, 'inspector_referente_id');
    }

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class, 'munimer_mapacalor.grupo_inspector', 'inspector_id', 'grupo_id')
            ->withTimestamps();
    }
}
