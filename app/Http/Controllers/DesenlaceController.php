<?php

namespace App\Http\Controllers;

use App\Models\Desenlace;
use Illuminate\Http\Request;

class DesenlaceController extends Controller
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

            $desenlace = Desenlace::create($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'desenlace' => $desenlace
                ]);
            }

            return back()->with('success', 'Desenlace creado exitosamente.');
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

    public function update(Request $request, Desenlace $desenlace)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'activo' => 'nullable|in:0,1',
            ]);

            $validated['activo'] = $request->input('activo', 0) == 1;

            $desenlace->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'desenlace' => $desenlace
                ]);
            }

            return back()->with('success', 'Desenlace actualizado exitosamente.');
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

    public function destroy(Desenlace $desenlace)
    {
        $desenlace->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Desenlace eliminado exitosamente.');
    }
}
