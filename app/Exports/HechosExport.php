<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel as ExcelWriter;

class HechosExport
{
    protected $hechos;

    public function __construct(Collection $hechos)
    {
        $this->hechos = $hechos;
    }

    public function export()
    {
        $data = [];

        // Encabezados
        $data[] = [
            'ID',
            'Fecha del Hecho',
            'Categoría',
            'Subcategoría',
            'Tipo Involucrado 1',
            'Sexo Involucrado 1',
            'Edad Involucrado 1',
            'Tipo Involucrado 2',
            'Sexo Involucrado 2',
            'Edad Involucrado 2',
            'Horario',
            'Tipo Domicilio',
            'Zona',
            'Barrio',
            'Domicilio',
            'Latitud',
            'Longitud',
            'Observaciones',
            'Usuario',
            'Fecha de Carga',
        ];

        // Datos
        foreach ($this->hechos as $hecho) {
            $data[] = [
                $hecho->id,
                $hecho->fecha_hecho ? $hecho->fecha_hecho->format('d/m/Y') : '',
                $hecho->categoria->nombre ?? '',
                $hecho->subcategoria->nombre ?? '',
                $hecho->tipoInvolucrado1->nombre ?? '',
                $hecho->sexo_involucrado1 ?? '',
                $hecho->edad_involucrado1 ?? '',
                $hecho->tipoInvolucrado2->nombre ?? '',
                $hecho->sexo_involucrado2 ?? '',
                $hecho->edad_involucrado2 ?? '',
                $hecho->horario->nombre ?? '',
                $hecho->tipo_domicilio ?? '',
                $hecho->zona ?? '',
                $hecho->barrio->nombre ?? '',
                $hecho->domicilio ?? '',
                $hecho->latitud ?? '',
                $hecho->longitud ?? '',
                $hecho->observaciones ?? '',
                $hecho->user->name ?? '',
                $hecho->created_at ? $hecho->created_at->format('d/m/Y H:i') : '',
            ];
        }

        return $data;
    }
}
