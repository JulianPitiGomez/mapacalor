<?php

namespace App\Http\Controllers;

use App\Models\TipoInvolucrado;
use Illuminate\Http\Request;

class TipoInvolucradoController extends Controller
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

            $tipoInvolucrado = TipoInvolucrado::create($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'tipoInvolucrado' => $tipoInvolucrado
                ]);
            }

            return back()->with('success', 'Tipo de involucrado creado exitosamente.');
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

    public function update(Request $request, TipoInvolucrado $tipoInvolucrado)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'activo' => 'nullable|in:0,1',
            ]);

            $validated['activo'] = $request->input('activo', 0) == 1;

            $tipoInvolucrado->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'tipoInvolucrado' => $tipoInvolucrado
                ]);
            }

            return back()->with('success', 'Tipo de involucrado actualizado exitosamente.');
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

    public function destroy(TipoInvolucrado $tipoInvolucrado)
    {
        $tipoInvolucrado->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Tipo de involucrado eliminado exitosamente.');
    }
}
