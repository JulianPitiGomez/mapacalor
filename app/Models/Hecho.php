<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hecho extends Model
{
    protected $fillable = [
        'fecha_hecho',
        'categoria_id',
        'subcategoria_id',
        'tipo_involucrado1_id',
        'sexo_involucrado1',
        'edad_involucrado1',
        'tipo_involucrado2_id',
        'sexo_involucrado2',
        'edad_involucrado2',
        'horario_id',
        'accion_id',
        'desenlace_id',
        'latitud',
        'longitud',
        'barrio_id',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'fecha_hecho' => 'date',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategoria(): BelongsTo
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function tipoInvolucrado1(): BelongsTo
    {
        return $this->belongsTo(TipoInvolucrado::class, 'tipo_involucrado1_id');
    }

    public function tipoInvolucrado2(): BelongsTo
    {
        return $this->belongsTo(TipoInvolucrado::class, 'tipo_involucrado2_id');
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }

    public function accion(): BelongsTo
    {
        return $this->belongsTo(Accion::class);
    }

    public function desenlace(): BelongsTo
    {
        return $this->belongsTo(Desenlace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }
}
