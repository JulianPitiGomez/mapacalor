<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Operativos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #fff;
        }

        /* ── Cabecera ─────────────────────────────────── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 16px 20px 12px;
            border-bottom: 2px solid #314158;
            margin-bottom: 12px;
        }
        .header-left h1 {
            font-size: 18px;
            font-weight: 700;
            color: #314158;
        }
        .header-left p {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }
        .header-right {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
        }

        /* ── Filtros aplicados ────────────────────────── */
        .filtros {
            margin: 0 20px 12px;
            padding: 8px 12px;
            background: #f3f4f6;
            border-radius: 6px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px 16px;
            font-size: 10px;
            color: #374151;
        }
        .filtros strong { color: #111827; }

        /* ── Resumen ──────────────────────────────────── */
        .resumen {
            margin: 0 20px 12px;
            font-size: 11px;
            color: #374151;
        }
        .resumen strong { font-size: 13px; color: #314158; }

        /* ── Tabla ────────────────────────────────────── */
        .tabla-wrap { padding: 0 20px; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        thead tr {
            background-color: #314158;
            color: #fff;
        }
        thead th {
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            font-size: 9px;
        }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        tbody tr:hover { background-color: #eef2ff; }
        tbody td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .td-id { font-weight: 700; color: #314158; white-space: nowrap; }
        .td-fecha { white-space: nowrap; }
        .td-hora { white-space: nowrap; color: #6b7280; }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 600;
        }
        .badge-planificado { background: #dbeafe; color: #1d4ed8; }
        .badge-en_curso    { background: #fef3c7; color: #92400e; }
        .badge-finalizado  { background: #d1fae5; color: #065f46; }
        .badge-cancelado   { background: #fee2e2; color: #991b1b; }

        /* ── Footer ───────────────────────────────────── */
        .footer {
            margin-top: 16px;
            padding: 8px 20px 0;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #9ca3af;
        }

        /* ── Print ────────────────────────────────────── */
        @media print {
            @page {
                size: A4 landscape;
                margin: 12mm 10mm;
            }
            body { font-size: 10px; }
            .no-print { display: none !important; }
            thead { display: table-header-group; }
            tbody tr { page-break-inside: avoid; }
        }

        /* Botón imprimir (solo pantalla) */
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 12px 20px;
            padding: 8px 16px;
            background: #314158;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-print:hover { background: #253348; }
    </style>
</head>
<body>

    {{-- Cabecera --}}
    <div class="header">
        <div class="header-left">
            <h1>Observatorio de Seguridad</h1>
            <p>Reporte de Operativos</p>
            <p style="margin-top:4px; font-size:10px; color:#6b7280;">Generado el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <div class="header-right">
            <div>Total: <strong>{{ $operativos->count() }}</strong> operativos</div>
        </div>
    </div>

    {{-- Filtros aplicados --}}
    @if(array_filter($filtros))
    <div class="filtros">
        <span><strong>Filtros:</strong></span>
        @if($filtros['fechaDesde'] || $filtros['fechaHasta'])
            <span>
                <strong>Período:</strong>
                {{ $filtros['fechaDesde'] ?? '—' }} al {{ $filtros['fechaHasta'] ?? '—' }}
            </span>
        @endif
        @if($filtros['departamento'])
            <span><strong>Departamento:</strong> {{ $filtros['departamento'] }}</span>
        @endif
        @if($filtros['estado'])
            <span><strong>Estado:</strong> {{ ucfirst(str_replace('_', ' ', $filtros['estado'])) }}</span>
        @endif
        @if($filtros['search'])
            <span><strong>Búsqueda:</strong> "{{ $filtros['search'] }}"</span>
        @endif
    </div>
    @endif

    {{-- Botón imprimir (solo en pantalla) --}}
    <button class="btn-print no-print" onclick="window.print()">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Imprimir / Guardar PDF
    </button>

    {{-- Tabla --}}
    <div class="tabla-wrap">
        @if($operativos->isEmpty())
            <p style="color:#6b7280; padding: 20px 0;">No se encontraron operativos para los filtros seleccionados.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th style="width:40px">ID</th>
                    <th style="width:70px">Fecha</th>
                    <th style="width:80px">Horario</th>
                    <th>Descripción</th>
                    <th>Lugar</th>
                    <th style="width:110px">Departamento</th>
                    <th style="width:120px">Referente</th>
                    <th style="width:75px">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($operativos as $op)
                <tr>
                    <td class="td-id">#{{ $op->id }}</td>
                    <td class="td-fecha">{{ $op->fecha->format('d/m/Y') }}</td>
                    <td class="td-hora">
                        {{ \Carbon\Carbon::parse($op->hora_desde)->format('H:i') }} –
                        {{ \Carbon\Carbon::parse($op->hora_hasta)->format('H:i') }}
                    </td>
                    <td>{{ $op->descripcion }}</td>
                    <td>{{ $op->lugar }}</td>
                    <td>{{ $op->departamento?->nombre ?? '—' }}</td>
                    <td>{{ $op->inspectorReferente?->nombre ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $op->estado }}">{{ $op->estado_label }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Footer --}}
    <div class="footer">
        <span>Municipalidad de Mercedes · Sistema de Gestión</span>
    </div>

    <script>
        // Auto-print solo cuando se accede directamente (no dentro de un iframe)
        if (window.self === window.top) {
            window.addEventListener('load', function () {
                window.print();
            });
        }
    </script>
</body>
</html>
