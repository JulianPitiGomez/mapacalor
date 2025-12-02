<?php

namespace App\Livewire;

use App\Models\Hecho;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Barrio;
use App\Models\TipoInvolucrado;
use App\Models\Horario;
use App\Models\Accion;
use App\Models\Desenlace;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Exports\HechosExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class Estadisticas extends Component
{
    // Filtros
    public $fechaDesde;
    public $fechaHasta;
    public $categoriaId = '';
    public $subcategoriaId = '';
    public $tipoInvolucrado1Id = '';
    public $tipoInvolucrado2Id = '';
    public $sexoInvolucrado1 = '';
    public $sexoInvolucrado2 = '';
    public $edadInvolucrado1Desde;
    public $edadInvolucrado1Hasta;
    public $edadInvolucrado2Desde;
    public $edadInvolucrado2Hasta;
    public $rangoEdadInv1 = ''; // Filtro por rango de edad involucrado 1
    public $rangoEdadInv2 = ''; // Filtro por rango de edad involucrado 2
    public $horarioId = '';
    public $accionId = '';
    public $desenlaceId = '';
    public $barrioId = '';

    // Estado del panel de filtros
    public $mostrarFiltros = false;

    // Datos para los selects
    public $categorias;
    public $subcategorias = [];
    public $barrios;
    public $tiposInvolucrados = [];
    public $horarios = [];
    public $acciones = [];
    public $desenlaces = [];

    // Datos calculados
    public $totalHechos = 0;
    public $hechosFiltrados = [];
    public $datosMapaCalor = [];
    public $datosMapaMarkers = [];
    public $estadisticasPorCategoria = [];
    public $estadisticasPorBarrio = [];
    public $estadisticasPorMes = [];
    public $estadisticasPorHorario = [];

    // Nuevas estadísticas de involucrados
    public $estadisticasPorTipoInvolucrado1 = [];
    public $estadisticasPorTipoInvolucrado2 = [];
    public $estadisticasPorSexoInvolucrado1 = [];
    public $estadisticasPorSexoInvolucrado2 = [];
    public $estadisticasPorRangoEdadInvolucrado1 = [];
    public $estadisticasPorRangoEdadInvolucrado2 = [];

    // Estadísticas de Acciones y Desenlaces
    public $estadisticasPorAccion = [];
    public $estadisticasPorDesenlace = [];

    public function mount()
    {
        // Inicializar fechas (último mes por defecto)
        $this->fechaHasta = date('Y-m-d');
        $this->fechaDesde = date('Y-m-d', strtotime('-1 month'));

        // Cargar catálogos
        $this->categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
        $this->barrios = Barrio::where('activo', true)->orderBy('nombre')->get();

        // Cargar datos iniciales
        $this->aplicarFiltros();
    }

    public function updatedCategoriaId()
    {
        if ($this->categoriaId) {
            $this->subcategorias = Subcategoria::where('categoria_id', $this->categoriaId)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->tiposInvolucrados = TipoInvolucrado::where('categoria_id', $this->categoriaId)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->horarios = Horario::where('categoria_id', $this->categoriaId)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->acciones = Accion::where('categoria_id', $this->categoriaId)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $this->desenlaces = Desenlace::where('categoria_id', $this->categoriaId)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();
        } else {
            $this->subcategorias = [];
            $this->tiposInvolucrados = [];
            $this->horarios = [];
            $this->acciones = [];
            $this->desenlaces = [];
        }

        $this->subcategoriaId = '';
        $this->tipoInvolucrado1Id = '';
        $this->tipoInvolucrado2Id = '';
        $this->horarioId = '';
        $this->accionId = '';
        $this->desenlaceId = '';

        $this->aplicarFiltros();
    }

    public function aplicarFiltros()
    {
        $query = Hecho::with(['categoria', 'subcategoria', 'barrio', 'tipoInvolucrado1', 'tipoInvolucrado2', 'horario', 'accion', 'desenlace', 'user']);

        // Aplicar filtros
        if ($this->fechaDesde) {
            $query->where('fecha_hecho', '>=', $this->fechaDesde);
        }

        if ($this->fechaHasta) {
            $query->where('fecha_hecho', '<=', $this->fechaHasta);
        }

        if ($this->categoriaId) {
            $query->where('categoria_id', $this->categoriaId);
        }

        if ($this->subcategoriaId) {
            $query->where('subcategoria_id', $this->subcategoriaId);
        }

        if ($this->tipoInvolucrado1Id) {
            $query->where('tipo_involucrado1_id', $this->tipoInvolucrado1Id);
        }

        if ($this->tipoInvolucrado2Id) {
            $query->where('tipo_involucrado2_id', $this->tipoInvolucrado2Id);
        }

        if ($this->sexoInvolucrado1) {
            $query->where('sexo_involucrado1', $this->sexoInvolucrado1);
        }

        if ($this->sexoInvolucrado2) {
            $query->where('sexo_involucrado2', $this->sexoInvolucrado2);
        }

        if ($this->edadInvolucrado1Desde) {
            $query->where('edad_involucrado1', '>=', $this->edadInvolucrado1Desde);
        }

        if ($this->edadInvolucrado1Hasta) {
            $query->where('edad_involucrado1', '<=', $this->edadInvolucrado1Hasta);
        }

        if ($this->edadInvolucrado2Desde) {
            $query->where('edad_involucrado2', '>=', $this->edadInvolucrado2Desde);
        }

        if ($this->edadInvolucrado2Hasta) {
            $query->where('edad_involucrado2', '<=', $this->edadInvolucrado2Hasta);
        }

        // Filtro por rango de edad involucrado 1
        if ($this->rangoEdadInv1) {
            $rangos = $this->obtenerRangosEdad();
            if (isset($rangos[$this->rangoEdadInv1])) {
                $rango = $rangos[$this->rangoEdadInv1];
                $query->where(function($q) use ($rango) {
                    $q->whereBetween('edad_involucrado1', [$rango['min'], $rango['max']])
                      ->orWhere('edad_involucrado1', null);
                });
            }
        }

        // Filtro por rango de edad involucrado 2
        if ($this->rangoEdadInv2) {
            $rangos = $this->obtenerRangosEdad();
            if (isset($rangos[$this->rangoEdadInv2])) {
                $rango = $rangos[$this->rangoEdadInv2];
                $query->where(function($q) use ($rango) {
                    $q->whereBetween('edad_involucrado2', [$rango['min'], $rango['max']])
                      ->orWhere('edad_involucrado2', null);
                });
            }
        }

        if ($this->horarioId) {
            $query->where('horario_id', $this->horarioId);
        }

        if ($this->accionId) {
            $query->where('accion_id', $this->accionId);
        }

        if ($this->desenlaceId) {
            $query->where('desenlace_id', $this->desenlaceId);
        }

        if ($this->barrioId) {
            $query->where('barrio_id', $this->barrioId);
        }

        // Obtener hechos filtrados
        $this->hechosFiltrados = $query->get();
        $this->totalHechos = $this->hechosFiltrados->count();

        // Calcular estadísticas
        $this->calcularEstadisticas();

        // Disparar evento JavaScript para actualizar visualizaciones
        $this->dispatch('actualizarEstadisticas');
    }

    private function calcularEstadisticas()
    {
        // Datos para mapa de calor y markers
        $this->datosMapaCalor = [];
        $this->datosMapaMarkers = [];

        foreach ($this->hechosFiltrados as $hecho) {
            if ($hecho->latitud && $hecho->longitud) {
                $this->datosMapaCalor[] = [
                    'lat' => (float) $hecho->latitud,
                    'lng' => (float) $hecho->longitud,
                ];

                $this->datosMapaMarkers[] = [
                    'lat' => (float) $hecho->latitud,
                    'lng' => (float) $hecho->longitud,
                    'titulo' => $hecho->categoria->nombre ?? 'Sin categoría',
                    'subcategoria' => $hecho->subcategoria->nombre ?? 'Sin subcategoría',
                    'subcategoria_id' => $hecho->subcategoria_id,
                    'descripcion' => ($hecho->subcategoria->nombre ?? '') . ' - ' . $hecho->fecha_hecho->format('d/m/Y'),
                    'barrio' => $hecho->barrio->nombre ?? 'Sin barrio',
                ];
            }
        }

        // Estadísticas por categoría
        $this->estadisticasPorCategoria = $this->hechosFiltrados
            ->groupBy('categoria_id')
            ->map(function ($items, $key) {
                return [
                    'categoria' => $items->first()->categoria->nombre ?? 'Sin categoría',
                    'cantidad' => $items->count(),
                ];
            })
            ->values()
            ->toArray();

        // Estadísticas por barrio
        $this->estadisticasPorBarrio = $this->hechosFiltrados
            ->groupBy('barrio_id')
            ->map(function ($items, $key) {
                return [
                    'barrio' => $items->first()->barrio->nombre ?? 'Sin barrio',
                    'cantidad' => $items->count(),
                ];
            })
            ->values()
            ->sortByDesc('cantidad')
            ->take(10)
            ->toArray();

        // Estadísticas por mes
        $this->estadisticasPorMes = $this->hechosFiltrados
            ->groupBy(function ($item) {
                return $item->fecha_hecho->format('Y-m');
            })
            ->map(function ($items, $key) {
                return [
                    'mes' => $key,
                    'cantidad' => $items->count(),
                ];
            })
            ->sortBy('mes')
            ->values()
            ->toArray();

        // Estadísticas por horario
        $this->estadisticasPorHorario = $this->hechosFiltrados
            ->groupBy('horario_id')
            ->map(function ($items, $key) {
                return [
                    'horario' => $items->first()->horario->nombre ?? 'Sin horario',
                    'cantidad' => $items->count(),
                ];
            })
            ->values()
            ->toArray();

        // === NUEVAS ESTADÍSTICAS DE INVOLUCRADOS ===

        // Estadísticas por Tipo de Involucrado 1
        $tipoInv1Stats = [];
        foreach ($this->hechosFiltrados as $hecho) {
            $tipo = $hecho->tipoInvolucrado1->nombre ?? 'Sin datos';
            if (!isset($tipoInv1Stats[$tipo])) {
                $tipoInv1Stats[$tipo] = 0;
            }
            $tipoInv1Stats[$tipo]++;
        }
        $this->estadisticasPorTipoInvolucrado1 = collect($tipoInv1Stats)
            ->map(function ($cantidad, $tipo) {
                return ['tipo' => $tipo, 'cantidad' => $cantidad];
            })
            ->values()
            ->toArray();

        // Estadísticas por Tipo de Involucrado 2
        $tipoInv2Stats = [];
        foreach ($this->hechosFiltrados as $hecho) {
            $tipo = $hecho->tipoInvolucrado2->nombre ?? 'Sin datos';
            if (!isset($tipoInv2Stats[$tipo])) {
                $tipoInv2Stats[$tipo] = 0;
            }
            $tipoInv2Stats[$tipo]++;
        }
        $this->estadisticasPorTipoInvolucrado2 = collect($tipoInv2Stats)
            ->map(function ($cantidad, $tipo) {
                return ['tipo' => $tipo, 'cantidad' => $cantidad];
            })
            ->values()
            ->toArray();

        // Estadísticas por Sexo de Involucrado 1
        $sexoInv1Stats = [];
        foreach ($this->hechosFiltrados as $hecho) {
            $sexo = $hecho->sexo_involucrado1 ?? 'Sin datos';
            if (!isset($sexoInv1Stats[$sexo])) {
                $sexoInv1Stats[$sexo] = 0;
            }
            $sexoInv1Stats[$sexo]++;
        }
        $this->estadisticasPorSexoInvolucrado1 = collect($sexoInv1Stats)
            ->map(function ($cantidad, $sexo) {
                return ['sexo' => $sexo, 'cantidad' => $cantidad];
            })
            ->values()
            ->toArray();

        // Estadísticas por Sexo de Involucrado 2
        $sexoInv2Stats = [];
        foreach ($this->hechosFiltrados as $hecho) {
            $sexo = $hecho->sexo_involucrado2 ?? 'Sin datos';
            if (!isset($sexoInv2Stats[$sexo])) {
                $sexoInv2Stats[$sexo] = 0;
            }
            $sexoInv2Stats[$sexo]++;
        }
        $this->estadisticasPorSexoInvolucrado2 = collect($sexoInv2Stats)
            ->map(function ($cantidad, $sexo) {
                return ['sexo' => $sexo, 'cantidad' => $cantidad];
            })
            ->values()
            ->toArray();

        // Estadísticas por Rango de Edad de Involucrado 1
        $rangoEdadInv1Stats = [];
        $rangos = $this->obtenerRangosEdad();
        foreach ($this->hechosFiltrados as $hecho) {
            $edad = $hecho->edad_involucrado1;
            // Tratar edad null o 0 como "Sin datos"
            if ($edad === null || $edad == 0) {
                $rango = 'Sin datos';
            } else {
                $rango = 'Sin datos';
                foreach ($rangos as $nombre => $limites) {
                    if ($edad >= $limites['min'] && $edad <= $limites['max']) {
                        $rango = $nombre;
                        break;
                    }
                }
            }
            if (!isset($rangoEdadInv1Stats[$rango])) {
                $rangoEdadInv1Stats[$rango] = 0;
            }
            $rangoEdadInv1Stats[$rango]++;
        }
        $this->estadisticasPorRangoEdadInvolucrado1 = collect($rangoEdadInv1Stats)
            ->map(function ($cantidad, $rango) {
                return ['rango' => $rango, 'cantidad' => $cantidad];
            })
            ->values()
            ->toArray();

        // Estadísticas por Rango de Edad de Involucrado 2
        $rangoEdadInv2Stats = [];
        foreach ($this->hechosFiltrados as $hecho) {
            $edad = $hecho->edad_involucrado2;
            // Tratar edad null o 0 como "Sin datos"
            if ($edad === null || $edad == 0) {
                $rango = 'Sin datos';
            } else {
                $rango = 'Sin datos';
                foreach ($rangos as $nombre => $limites) {
                    if ($edad >= $limites['min'] && $edad <= $limites['max']) {
                        $rango = $nombre;
                        break;
                    }
                }
            }
            if (!isset($rangoEdadInv2Stats[$rango])) {
                $rangoEdadInv2Stats[$rango] = 0;
            }
            $rangoEdadInv2Stats[$rango]++;
        }
        $this->estadisticasPorRangoEdadInvolucrado2 = collect($rangoEdadInv2Stats)
            ->map(function ($cantidad, $rango) {
                return ['rango' => $rango, 'cantidad' => $cantidad];
            })
            ->values()
            ->toArray();

        // Estadísticas por Acción
        $accionStats = [];
        foreach ($this->hechosFiltrados as $hecho) {
            $accion = $hecho->accion->nombre ?? 'Sin datos';
            if (!isset($accionStats[$accion])) {
                $accionStats[$accion] = 0;
            }
            $accionStats[$accion]++;
        }
        $this->estadisticasPorAccion = collect($accionStats)
            ->map(function ($cantidad, $accion) {
                return ['accion' => $accion, 'cantidad' => $cantidad];
            })
            ->values()
            ->toArray();

        // Estadísticas por Desenlace
        $desenlaceStats = [];
        foreach ($this->hechosFiltrados as $hecho) {
            $desenlace = $hecho->desenlace->nombre ?? 'Sin datos';
            if (!isset($desenlaceStats[$desenlace])) {
                $desenlaceStats[$desenlace] = 0;
            }
            $desenlaceStats[$desenlace]++;
        }
        $this->estadisticasPorDesenlace = collect($desenlaceStats)
            ->map(function ($cantidad, $desenlace) {
                return ['desenlace' => $desenlace, 'cantidad' => $cantidad];
            })
            ->values()
            ->toArray();
    }

    private function obtenerRangosEdad()
    {
        return [
            '1-17' => ['min' => 1, 'max' => 17],
            '18-25' => ['min' => 18, 'max' => 25],
            '26-35' => ['min' => 26, 'max' => 35],
            '36-50' => ['min' => 36, 'max' => 50],
            '51-65' => ['min' => 51, 'max' => 65],
            '66+' => ['min' => 66, 'max' => 120],
        ];
    }

    public function limpiarFiltros()
    {
        $this->fechaHasta = date('Y-m-d');
        $this->fechaDesde = date('Y-m-d', strtotime('-1 month'));
        $this->categoriaId = '';
        $this->subcategoriaId = '';
        $this->tipoInvolucrado1Id = '';
        $this->tipoInvolucrado2Id = '';
        $this->sexoInvolucrado1 = '';
        $this->sexoInvolucrado2 = '';
        $this->edadInvolucrado1Desde = null;
        $this->edadInvolucrado1Hasta = null;
        $this->edadInvolucrado2Desde = null;
        $this->edadInvolucrado2Hasta = null;
        $this->rangoEdadInv1 = '';
        $this->rangoEdadInv2 = '';
        $this->horarioId = '';
        $this->accionId = '';
        $this->desenlaceId = '';
        $this->barrioId = '';

        $this->subcategorias = [];
        $this->tiposInvolucrados = [];
        $this->horarios = [];
        $this->acciones = [];
        $this->desenlaces = [];

        $this->aplicarFiltros();
    }

    public function toggleFiltros()
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }

    public function exportarExcel()
    {
        $nombreArchivo = 'hechos_' . date('Y-m-d_His') . '.xls';

        $export = new HechosExport($this->hechosFiltrados);
        $data = $export->export();

        // Crear HTML table que Excel puede abrir directamente
        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<table border="1">';

        foreach ($data as $index => $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                // Primera fila en negrita (encabezados)
                if ($index === 0) {
                    $html .= '<th>' . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . '</th>';
                } else {
                    $html .= '<td>' . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . '</td>';
                }
            }
            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        return Response::streamDownload(function() use ($html) {
            echo "\xEF\xBB\xBF"; // BOM UTF-8
            echo $html;
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ]);
    }

    public function exportarPDF()
    {
        $pdf = Pdf::loadView('exports.hechos-pdf', [
            'hechos' => $this->hechosFiltrados,
            'totalHechos' => $this->totalHechos,
            'fechaDesde' => $this->fechaDesde,
            'fechaHasta' => $this->fechaHasta,
            'estadisticasPorCategoria' => $this->estadisticasPorCategoria,
            'estadisticasPorBarrio' => $this->estadisticasPorBarrio,
        ]);

        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'hechos_' . date('Y-m-d_His') . '.pdf');
    }

    public function render()
    {
        return view('livewire.estadisticas');
    }
}
