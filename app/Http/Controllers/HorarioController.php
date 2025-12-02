<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
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

            $horario = Horario::create($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'horario' => $horario
                ]);
            }

            return back()->with('success', 'Horario creado exitosamente.');
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

    public function update(Request $request, Horario $horario)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'activo' => 'nullable|in:0,1',
            ]);

            $validated['activo'] = $request->input('activo', 0) == 1;

            $horario->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'horario' => $horario
                ]);
            }

            return back()->with('success', 'Horario actualizado exitosamente.');
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

    public function destroy(Horario $horario)
    {
        $horario->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Horario eliminado exitosamente.');
    }
}
