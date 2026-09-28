<?php

use App\Http\Controllers\AccionController;
use App\Http\Controllers\BarrioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DesenlaceController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\HechoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\OperativoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\TipoInvolucradoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/estadisticas', function () {
    return view('estadisticas');
})->middleware(['auth', 'verified', 'solapa:estadisticas'])->name('estadisticas');

// Mantener dashboard como alias: manda a la primera solapa habilitada del usuario
// (un visualizador puede no tener Estadísticas).
Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->rutaInicio());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // El perfil propio siempre es editable, incluso para un visualizador.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // A partir de acá, un visualizador solo puede mirar.
    Route::middleware('bloquear_edicion')->group(function () {
        // CRUD de Categorías (incluye los catálogos secundarios, que se editan
        // desde la pantalla de categorías vía AJAX/modal)
        Route::middleware('solapa:categorias')->group(function () {
            Route::resource('categorias', CategoriaController::class);

            Route::post('subcategorias', [SubcategoriaController::class, 'store'])->name('subcategorias.store');
            Route::put('subcategorias/{subcategoria}', [SubcategoriaController::class, 'update'])->name('subcategorias.update');
            Route::delete('subcategorias/{subcategoria}', [SubcategoriaController::class, 'destroy'])->name('subcategorias.destroy');

            Route::post('tipos-involucrados', [TipoInvolucradoController::class, 'store'])->name('tipos-involucrados.store');
            Route::put('tipos-involucrados/{tipoInvolucrado}', [TipoInvolucradoController::class, 'update'])->name('tipos-involucrados.update');
            Route::delete('tipos-involucrados/{tipoInvolucrado}', [TipoInvolucradoController::class, 'destroy'])->name('tipos-involucrados.destroy');

            Route::post('horarios', [HorarioController::class, 'store'])->name('horarios.store');
            Route::put('horarios/{horario}', [HorarioController::class, 'update'])->name('horarios.update');
            Route::delete('horarios/{horario}', [HorarioController::class, 'destroy'])->name('horarios.destroy');

            Route::post('acciones', [AccionController::class, 'store'])->name('acciones.store');
            Route::put('acciones/{accion}', [AccionController::class, 'update'])->name('acciones.update');
            Route::delete('acciones/{accion}', [AccionController::class, 'destroy'])->name('acciones.destroy');

            Route::post('desenlaces', [DesenlaceController::class, 'store'])->name('desenlaces.store');
            Route::put('desenlaces/{desenlace}', [DesenlaceController::class, 'update'])->name('desenlaces.update');
            Route::delete('desenlaces/{desenlace}', [DesenlaceController::class, 'destroy'])->name('desenlaces.destroy');
        });

        // CRUD de Barrios
        Route::middleware('solapa:barrios')->group(function () {
            Route::resource('barrios', BarrioController::class)->except(['show']);
        });

        // Gestión de Hechos
        Route::middleware('solapa:hechos')->group(function () {
            Route::get('hechos', [HechoController::class, 'index'])->name('hechos.index');
            Route::get('hechos/create', [HechoController::class, 'create'])->name('hechos.create');
            Route::get('hechos/{hecho}/edit', [HechoController::class, 'edit'])->name('hechos.edit');
        });

        // Estadísticas de Operativos
        Route::middleware('solapa:estadisticas-operativos')->group(function () {
            Route::get('estadisticas-operativos', function () {
                return view('estadisticas-operativos.index');
            })->name('estadisticas-operativos.index');
        });

        // Estadísticas de Actas (actas simples de faltas, sin las de operativos)
        Route::middleware('solapa:estadisticas-actas')->group(function () {
            Route::get('estadisticas-actas', function () {
                return view('estadisticas-actas.index');
            })->name('estadisticas-actas.index');
        });

        // Gestión de Operativos
        Route::middleware('solapa:operativos')->group(function () {
            Route::get('operativos', [OperativoController::class, 'index'])->name('operativos.index');
            Route::get('operativos/reporte', [OperativoController::class, 'reporte'])->name('operativos.reporte');
            Route::get('operativos/exportar', [OperativoController::class, 'exportar'])->name('operativos.exportar');
            Route::get('operativos/create', [OperativoController::class, 'create'])->name('operativos.create');
            Route::get('operativos/{operativo}/edit', [OperativoController::class, 'edit'])->name('operativos.edit');
        });

        // Gestión de Grupos
        Route::middleware('solapa:grupos')->group(function () {
            Route::get('grupos', [GrupoController::class, 'index'])->name('grupos.index');
            Route::get('grupos/create', [GrupoController::class, 'create'])->name('grupos.create');
            Route::get('grupos/{grupo}/edit', [GrupoController::class, 'edit'])->name('grupos.edit');
        });

        // Gestión de Usuarios: exclusiva de supervisores
        Route::middleware('es_supervisor')->group(function () {
            Route::get('usuarios', [UserController::class, 'index'])->name('usuarios.index');
            Route::get('usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
            Route::get('usuarios/{user}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
        });
    });
});

require __DIR__.'/auth.php';
