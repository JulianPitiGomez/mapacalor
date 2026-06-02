<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount(['subcategorias', 'tiposInvolucrados', 'horarios', 'acciones', 'desenlaces'])
            ->orderBy('nombre')
            ->paginate(15);
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'etiquetas' => 'nullable|array',
            'etiquetas.*.nombre' => 'required_with:etiquetas|string|max:100',
            'etiquetas.*.tipo' => 'nullable|string|in:texto,numero,fecha,hora,textarea,select',
            'etiquetas.*.longitud' => 'nullable|integer|min:1|max:500',
            'etiquetas.*.opciones' => 'nullable|string',
            'etiquetas.*.requerido' => 'nullable',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->has('activo');

        // Procesar etiquetas
        if (isset($validated['etiquetas'])) {
            $validated['etiquetas'] = $this->procesarEtiquetas($validated['etiquetas']);
        }

        Categoria::create($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Categoria $categoria)
    {
        $categoria->load(['subcategorias', 'tiposInvolucrados', 'horarios']);
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'etiquetas' => 'nullable|array',
            'etiquetas.*.nombre' => 'required_with:etiquetas|string|max:100',
            'etiquetas.*.tipo' => 'nullable|string|in:texto,numero,fecha,hora,textarea,select',
            'etiquetas.*.longitud' => 'nullable|integer|min:1|max:500',
            'etiquetas.*.opciones' => 'nullable|string',
            'etiquetas.*.requerido' => 'nullable',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->has('activo');

        // Procesar etiquetas
        if (isset($validated['etiquetas'])) {
            $validated['etiquetas'] = $this->procesarEtiquetas($validated['etiquetas']);
        } else {
            $validated['etiquetas'] = [];
        }

        $categoria->update($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Procesar y limpiar etiquetas
     */
    private function procesarEtiquetas(array $etiquetas): array
    {
        $resultado = [];

        foreach ($etiquetas as $etiqueta) {
            // Saltar si no tiene nombre
            if (empty(trim($etiqueta['nombre'] ?? ''))) {
                continue;
            }

            $item = [
                'nombre' => trim($etiqueta['nombre']),
                'tipo' => $etiqueta['tipo'] ?? 'texto',
                'requerido' => isset($etiqueta['requerido']) && $etiqueta['requerido'],
            ];

            // Agregar longitud solo para tipo texto
            if ($item['tipo'] === 'texto' && !empty($etiqueta['longitud'])) {
                $item['longitud'] = (int) $etiqueta['longitud'];
            }

            // Agregar opciones solo para tipo select
            if ($item['tipo'] === 'select' && !empty($etiqueta['opciones'])) {
                $opciones = array_map('trim', explode(',', $etiqueta['opciones']));
                $item['opciones'] = array_values(array_filter($opciones));
            }

            $resultado[] = $item;
        }

        return $resultado;
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}
