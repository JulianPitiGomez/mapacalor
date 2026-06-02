<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\BarrioController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\TipoInvolucradoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AccionController;
use App\Http\Controllers\DesenlaceController;
use App\Http\Controllers\HechoController;
use App\Http\Controllers\OperativoController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/estadisticas', function () {
    return view('estadisticas');
})->middleware(['auth', 'verified'])->name('estadisticas');

// Mantener dashboard como alias para backward compatibility
Route::get('/dashboard', function () {
    return redirect()->route('estadisticas');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD de Categorías
    Route::resource('categorias', CategoriaController::class);

    // CRUD de Barrios
    Route::resource('barrios', BarrioController::class)->except(['show']);

    // Rutas para gestionar subcategorías, tipos involucrados y horarios (usados via AJAX/modal)
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

    // Gestión de Hechos
    Route::get('hechos', [HechoController::class, 'index'])->name('hechos.index');
    Route::get('hechos/create', [HechoController::class, 'create'])->name('hechos.create');
    Route::get('hechos/{hecho}/edit', [HechoController::class, 'edit'])->name('hechos.edit');

    // Rutas solo para supervisores
    Route::middleware('es_supervisor')->group(function () {
        // Gestión de Operativos
        Route::get('operativos', [OperativoController::class, 'index'])->name('operativos.index');
        Route::get('operativos/create', [OperativoController::class, 'create'])->name('operativos.create');
        Route::get('operativos/{operativo}/edit', [OperativoController::class, 'edit'])->name('operativos.edit');

        // Gestión de Grupos
        Route::get('grupos', [GrupoController::class, 'index'])->name('grupos.index');
        Route::get('grupos/create', [GrupoController::class, 'create'])->name('grupos.create');
        Route::get('grupos/{grupo}/edit', [GrupoController::class, 'edit'])->name('grupos.edit');

        // Gestión de Usuarios
        Route::get('usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
        Route::get('usuarios/{user}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
    });
});

require __DIR__.'/auth.php';
