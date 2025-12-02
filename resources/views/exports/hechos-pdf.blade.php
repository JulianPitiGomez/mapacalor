<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Hechos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #77BF43;
        }

        .header h1 {
            color: #314158;
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .stats-section {
            margin: 20px 0;
            display: flex;
            justify-content: space-around;
        }

        .stat-box {
            text-align: center;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
            flex: 1;
            margin: 0 5px;
        }

        .stat-box h3 {
            margin: 0;
            color: #77BF43;
            font-size: 24px;
        }

        .stat-box p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 9px;
        }

        .charts-section {
            margin: 20px 0;
        }

        .chart-title {
            font-weight: bold;
            color: #314158;
            margin: 15px 0 5px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table th {
            background-color: #314158;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9px;
        }

        table td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 8px;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Hechos Registrados</h1>
        <p>Período: {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-section">
        <div class="stat-box">
            <h3>{{ $totalHechos }}</h3>
            <p>Total de Hechos</p>
        </div>
        <div class="stat-box">
            <h3>{{ count($estadisticasPorCategoria) }}</h3>
            <p>Categorías</p>
        </div>
        <div class="stat-box">
            <h3>{{ count($estadisticasPorBarrio) }}</h3>
            <p>Barrios</p>
        </div>
    </div>

    <!-- Estadísticas por Categoría -->
    @if(count($estadisticasPorCategoria) > 0)
    <div class="charts-section">
        <div class="chart-title">Hechos por Categoría</div>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th style="text-align: center; width: 100px;">Cantidad</th>
                    <th style="text-align: center; width: 80px;">Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estadisticasPorCategoria as $stat)
                <tr>
                    <td>{{ $stat['categoria'] }}</td>
                    <td style="text-align: center;">{{ $stat['cantidad'] }}</td>
                    <td style="text-align: center;">{{ number_format(($stat['cantidad'] / $totalHechos) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Estadísticas por Barrio -->
    @if(count($estadisticasPorBarrio) > 0)
    <div class="charts-section">
        <div class="chart-title">Top 10 Barrios con Mayor Cantidad de Hechos</div>
        <table>
            <thead>
                <tr>
                    <th>Barrio</th>
                    <th style="text-align: center; width: 100px;">Cantidad</th>
                    <th style="text-align: center; width: 80px;">Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estadisticasPorBarrio as $stat)
                <tr>
                    <td>{{ $stat['barrio'] }}</td>
                    <td style="text-align: center;">{{ $stat['cantidad'] }}</td>
                    <td style="text-align: center;">{{ number_format(($stat['cantidad'] / $totalHechos) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Detalle de Hechos -->
    <div class="charts-section" style="page-break-before: always;">
        <div class="chart-title">Detalle de Hechos (Primeros 100 registros)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">Fecha</th>
                    <th>Categoría</th>
                    <th>Subcategoría</th>
                    <th>Barrio</th>
                    <th>Domicilio</th>
                    <th style="width: 50px;">Tipo Dom.</th>
                    <th style="width: 40px;">Zona</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hechos->take(100) as $hecho)
                <tr>
                    <td>{{ $hecho->fecha_hecho->format('d/m/Y') }}</td>
                    <td>{{ $hecho->categoria->nombre ?? '-' }}</td>
                    <td>{{ $hecho->subcategoria->nombre ?? '-' }}</td>
                    <td>{{ $hecho->barrio->nombre ?? '-' }}</td>
                    <td>{{ $hecho->domicilio ?? '-' }}</td>
                    <td>{{ $hecho->tipo_domicilio ?? '-' }}</td>
                    <td>{{ $hecho->zona ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($hechos->count() > 100)
        <p style="font-style: italic; color: #666; margin-top: 10px;">
            * Mostrando 100 de {{ $totalHechos }} registros. Para ver todos los datos, exporte a Excel.
        </p>
        @endif
    </div>

    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Mapa de Calor</p>
    </div>
</body>
</html>
