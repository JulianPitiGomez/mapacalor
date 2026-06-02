<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    protected $connection = 'mysql_faltas';
    protected $table = 'fa_departamento';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;

    public function inspectores(): HasMany
    {
        return $this->hasMany(Inspector::class, 'dto_id');
    }

    public function operativos()
    {
        return $this->hasMany(Operativo::class, 'departamento_id');
    }
}
