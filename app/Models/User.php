<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROL_NORMAL = 'normal';

    public const ROL_SUPERVISOR = 'supervisor';

    public const ROL_VISUALIZADOR = 'visualizador';

    /**
     * Solapas del sistema que se le pueden habilitar a un visualizador.
     * La gestión de Usuarios no está acá: es exclusiva de los supervisores.
     *
     * @var array<string, array{label: string, ruta: string}>
     */
    public const SOLAPAS = [
        'estadisticas' => ['label' => 'Estadísticas', 'ruta' => 'estadisticas'],
        'hechos' => ['label' => 'Hechos', 'ruta' => 'hechos.index'],
        'categorias' => ['label' => 'Categorías', 'ruta' => 'categorias.index'],
        'barrios' => ['label' => 'Barrios', 'ruta' => 'barrios.index'],
        'operativos' => ['label' => 'Operativos', 'ruta' => 'operativos.index'],
        'estadisticas-operativos' => ['label' => 'Estadísticas Operativos', 'ruta' => 'estadisticas-operativos.index'],
        'estadisticas-actas' => ['label' => 'Estadísticas Actas', 'ruta' => 'estadisticas-actas.index'],
        'grupos' => ['label' => 'Grupos', 'ruta' => 'grupos.index'],
    ];

    /**
     * Solapas que ve cualquier usuario con rol normal.
     *
     * @var list<string>
     */
    public const SOLAPAS_BASICAS = ['estadisticas', 'hechos', 'categorias', 'barrios'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'es_supervisor',
        'rol',
        'solapas',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'es_supervisor' => 'boolean',
            'solapas' => 'array',
        ];
    }

    public function esSupervisor(): bool
    {
        return $this->rol === self::ROL_SUPERVISOR;
    }

    public function esVisualizador(): bool
    {
        return $this->rol === self::ROL_VISUALIZADOR;
    }

    /**
     * Un visualizador solo mira: no puede crear, editar ni eliminar nada.
     */
    public function puedeEditar(): bool
    {
        return ! $this->esVisualizador();
    }

    /**
     * Slugs de las solapas que este usuario tiene habilitadas (sin contar Usuarios).
     *
     * @return list<string>
     */
    public function solapasVisibles(): array
    {
        if ($this->esSupervisor()) {
            return array_keys(self::SOLAPAS);
        }

        if ($this->esVisualizador()) {
            return array_values(array_intersect(array_keys(self::SOLAPAS), $this->solapas ?? []));
        }

        return self::SOLAPAS_BASICAS;
    }

    public function puedeVer(string $solapa): bool
    {
        return in_array($solapa, $this->solapasVisibles(), true);
    }

    /**
     * Ruta a la que mandar al usuario al entrar: su primera solapa habilitada.
     * Un visualizador sin solapas se queda en su perfil.
     */
    public function rutaInicio(): string
    {
        $solapas = $this->solapasVisibles();

        if ($solapas === []) {
            return 'profile.edit';
        }

        return self::SOLAPAS[$solapas[0]]['ruta'];
    }

    public function getRolLabelAttribute(): string
    {
        return match ($this->rol) {
            self::ROL_SUPERVISOR => 'Supervisor',
            self::ROL_VISUALIZADOR => 'Visualizador',
            default => 'Normal',
        };
    }
}
