<?php

namespace App\Http\Controllers;

use App\Models\Barrio;
use Illuminate\Http\Request;

class BarrioController extends Controller
{
    public function index()
    {
        $barrios = Barrio::orderBy('nombre')->paginate(15);
        return view('barrios.index', compact('barrios'));
    }

    public function create()
    {
        return view('barrios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->has('activo');

        Barrio::create($validated);

        return redirect()->route('barrios.index')
            ->with('success', 'Barrio creado exitosamente.');
    }

    public function edit(Barrio $barrio)
    {
        return view('barrios.edit', compact('barrio'));
    }

    public function update(Request $request, Barrio $barrio)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->has('activo');

        $barrio->update($validated);

        return redirect()->route('barrios.index')
            ->with('success', 'Barrio actualizado exitosamente.');
    }

    public function destroy(Barrio $barrio)
    {
        $barrio->delete();

        return redirect()->route('barrios.index')
            ->with('success', 'Barrio eliminado exitosamente.');
    }
}
