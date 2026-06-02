<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Operativo extends Model
{
    protected $fillable = [
        'descripcion',
        'lugar',
        'latitud',
        'longitud',
        'fecha',
        'hora_desde',
        'hora_hasta',
        'hora_apertura_real',
        'hora_cierre_real',
        'acompanamiento_policial',
        'estado',
        'departamento_id',
        'inspector_referente_id',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function inspectorReferente(): BelongsTo
    {
        return $this->belongsTo(Inspector::class, 'inspector_referente_id');
    }

    public function inspectores(): BelongsToMany
    {
        return $this->belongsToMany(Inspector::class, 'munimer_mapacalor.operativo_inspector', 'operativo_id', 'inspector_id')
            ->withPivot('estado', 'observacion')
            ->withTimestamps();
    }

    public function getEstadoBadgeClassAttribute(): string
    {
        return match($this->estado) {
            'planificado' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'en_curso' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'finalizado' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'cancelado' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'planificado' => 'Planificado',
            'en_curso' => 'En Curso',
            'finalizado' => 'Finalizado',
            'cancelado' => 'Cancelado',
            default => $this->estado,
        };
    }
}
