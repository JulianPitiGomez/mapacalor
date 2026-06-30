<?php

namespace App\Http\Controllers;

use App\Models\Operativo;
use App\Models\Departamento;
use App\Models\Inspector;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OperativoController extends Controller
{
    public function index()
    {
        return view('operativos.index');
    }

    public function create()
    {
        return view('operativos.create');
    }

    public function edit(Operativo $operativo)
    {
        return view('operativos.edit', compact('operativo'));
    }

    public function reporte(Request $request)
    {
        $query = Operativo::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('id', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%')
                  ->orWhere('lugar', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filterDepartamento) {
            $query->where('departamento_id', $request->filterDepartamento);
        }

        if ($request->filterEstado) {
            $query->where('estado', $request->filterEstado);
        }

        if ($request->filterFechaDesde) {
            $query->whereDate('fecha', '>=', $request->filterFechaDesde);
        }

        if ($request->filterFechaHasta) {
            $query->whereDate('fecha', '<=', $request->filterFechaHasta);
        }

        $sortableFields = ['id', 'fecha', 'descripcion', 'lugar', 'estado'];
        $sortField      = in_array($request->sortField, $sortableFields) ? $request->sortField : 'fecha';
        $sortDirection  = $request->sortDirection === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortField, $sortDirection);
        if ($sortField === 'fecha') {
            $query->orderBy('hora_desde', $sortDirection);
        }

        $operativos = $query->get();

        $departamentosMap = Departamento::all()->keyBy('id');
        $inspectoresMap   = Inspector::all()->keyBy('id');

        foreach ($operativos as $op) {
            $op->setRelation('departamento', $departamentosMap->get($op->departamento_id));
            $op->setRelation('inspectorReferente', $inspectoresMap->get($op->inspector_referente_id));
        }

        $filtros = [
            'fechaDesde'   => $request->filterFechaDesde
                ? Carbon::parse($request->filterFechaDesde)->format('d/m/Y') : null,
            'fechaHasta'   => $request->filterFechaHasta
                ? Carbon::parse($request->filterFechaHasta)->format('d/m/Y') : null,
            'departamento' => $request->filterDepartamento
                ? ($departamentosMap->get($request->filterDepartamento)?->nombre) : null,
            'estado'       => $request->filterEstado,
            'search'       => $request->search,
        ];

        return view('operativos.reporte', compact('operativos', 'filtros'));
    }

    public function exportar(Request $request)
    {
        $query = Operativo::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('id', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%')
                  ->orWhere('lugar', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filterDepartamento) {
            $query->where('departamento_id', $request->filterDepartamento);
        }

        if ($request->filterEstado) {
            $query->where('estado', $request->filterEstado);
        }

        if ($request->filterFechaDesde) {
            $query->whereDate('fecha', '>=', $request->filterFechaDesde);
        }

        if ($request->filterFechaHasta) {
            $query->whereDate('fecha', '<=', $request->filterFechaHasta);
        }

        $sortableFields = ['id', 'fecha', 'descripcion', 'lugar', 'estado'];
        $sortField      = in_array($request->sortField, $sortableFields) ? $request->sortField : 'fecha';
        $sortDirection  = $request->sortDirection === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortField, $sortDirection);
        if ($sortField === 'fecha') {
            $query->orderBy('hora_desde', $sortDirection);
        }

        $operativos = $query->get();

        $departamentosMap = Departamento::all()->keyBy('id');
        $inspectoresMap   = Inspector::all()->keyBy('id');

        $filename = 'operativos_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ];

        $callback = function () use ($operativos, $departamentosMap, $inspectoresMap) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel

            fputcsv($file, [
                'ID', 'Fecha', 'Hora Desde', 'Hora Hasta',
                'Descripción', 'Lugar',
                'Departamento', 'Inspector Referente',
                'Estado', 'Acomp. Policial', 'Observaciones',
            ], ';');

            foreach ($operativos as $op) {
                $dpto      = $departamentosMap->get($op->departamento_id);
                $inspector = $inspectoresMap->get($op->inspector_referente_id);

                fputcsv($file, [
                    $op->id,
                    $op->fecha->format('d/m/Y'),
                    Carbon::parse($op->hora_desde)->format('H:i'),
                    Carbon::parse($op->hora_hasta)->format('H:i'),
                    $op->descripcion,
                    $op->lugar,
                    $dpto?->nombre ?? '-',
                    $inspector?->nombre ?? '-',
                    $op->estado_label,
                    $op->acompanamiento_policial ?? '-',
                    $op->observaciones ?? '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
