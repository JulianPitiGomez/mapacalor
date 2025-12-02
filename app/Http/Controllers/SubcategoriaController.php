<?php

namespace App\Http\Controllers;

use App\Models\Subcategoria;
use Illuminate\Http\Request;

class SubcategoriaController extends Controller
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

            $subcategoria = Subcategoria::create($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'subcategoria' => $subcategoria
                ]);
            }

            return back()->with('success', 'Subcategoría creada exitosamente.');
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

    public function update(Request $request, Subcategoria $subcategoria)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'activo' => 'nullable|in:0,1',
            ]);

            $validated['activo'] = $request->input('activo', 0) == 1;

            $subcategoria->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'subcategoria' => $subcategoria
                ]);
            }

            return back()->with('success', 'Subcategoría actualizada exitosamente.');
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

    public function destroy(Subcategoria $subcategoria)
    {
        $subcategoria->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Subcategoría eliminada exitosamente.');
    }
}
