<?php

namespace App\Http\Controllers;

use App\Models\Accion;
use Illuminate\Http\Request;

class AccionController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'categoria_id' => 'required|exists:categorias,id',
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'activo' => 'nullable|in:0,1',
            ]);

            $validated['activo'] = $request->input('activo', 0) == 1;

            $accion = Accion::create($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'accion' => $accion
                ]);
            }

            return back()->with('success', 'Acción creada exitosamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, Accion $accion)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'activo' => 'nullable|in:0,1',
            ]);

            $validated['activo'] = $request->input('activo', 0) == 1;

            $accion->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'accion' => $accion
                ]);
            }

            return back()->with('success', 'Acción actualizada exitosamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Accion $accion)
    {
        $accion->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Acción eliminada exitosamente.');
    }
}
