<div>
    {{-- Panel de Filtros Colapsable --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6">
        <div class="p-4 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <button wire:click="toggleFiltros"
                    class="flex items-center space-x-2 text-lg font-semibold text-gray-900 dark:text-gray-100 hover:text-primary transition">
                <svg class="w-5 h-5 transform transition-transform {{ $mostrarFiltros ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Filtros</span>
                @if(!$mostrarFiltros)
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(Click para mostrar)</span>
                @endif
            </button>
            <div class="flex gap-2">
                <button wire:click="limpiarFiltros"
                        class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-md transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Limpiar
                </button>
                <button wire:click="aplicarFiltros"
                        class="inline-flex items-center px-3 py-2 bg-primary hover:bg-primary-600 text-white text-sm rounded-md transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Aplicar
                </button>
            </div>
        </div>

        @if($mostrarFiltros)
        <div class="p-6"  x-data x-show="true" x-transition>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Fechas --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Desde</label>
                <input type="date" wire:model.live="fechaDesde"
                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Hasta</label>
                <input type="date" wire:model.live="fechaHasta"
                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
            </div>

            {{-- Categoría --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoría</label>
                <select wire:model.live="categoriaId"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                    <option value="">Todas</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Subcategoría --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subcategoría</label>
                <select wire:model.live="subcategoriaId"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                        @if(count($subcategorias) == 0) disabled @endif>
                    <option value="">Todas</option>
                    @foreach($subcategorias as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Barrio --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barrio</label>
                <select wire:model.live="barrioId"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                    <option value="">Todos</option>
                    @foreach($barrios as $barrio)
                        <option value="{{ $barrio->id }}">{{ $barrio->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Acción --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acción</label>
                <select wire:model.live="accionId"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                        @disabled(!$categoriaId)>
                    <option value="">Todas</option>
                    @foreach($acciones as $accion)
                        <option value="{{ $accion->id }}">{{ $accion->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Desenlace --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desenlace</label>
                <select wire:model.live="desenlaceId"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                        @disabled(!$categoriaId)>
                    <option value="">Todos</option>
                    @foreach($desenlaces as $desenlace)
                        <option value="{{ $desenlace->id }}">{{ $desenlace->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sexo Involucrado 1 --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sexo Inv. 1</label>
                <select wire:model.live="sexoInvolucrado1"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                    <option value="">Todos</option>
                    <option value="Varon">Varón</option>
                    <option value="Mujer">Mujer</option>
                </select>
            </div>

            {{-- Sexo Involucrado 2 --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sexo Inv. 2</label>
                <select wire:model.live="sexoInvolucrado2"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                    <option value="">Todos</option>
                    <option value="Varon">Varón</option>
                    <option value="Mujer">Mujer</option>
                </select>
            </div>

            {{-- Rango de Edad Inv. 1 --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rango Edad Inv. 1</label>
                <select wire:model.live="rangoEdadInv1"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                    <option value="">Todos</option>
                    <option value="1-17">1-17 años</option>
                    <option value="18-25">18-25 años</option>
                    <option value="26-35">26-35 años</option>
                    <option value="36-50">36-50 años</option>
                    <option value="51-65">51-65 años</option>
                    <option value="66+">66+ años</option>
                </select>
            </div>

            {{-- Rango de Edad Inv. 2 --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rango Edad Inv. 2</label>
                <select wire:model.live="rangoEdadInv2"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                    <option value="">Todos</option>
                    <option value="1-17">1-17 años</option>
                    <option value="18-25">18-25 años</option>
                    <option value="26-35">26-35 años</option>
                    <option value="36-50">36-50 años</option>
                    <option value="51-65">51-65 años</option>
                    <option value="66+">66+ años</option>
                </select>
            </div>
        </div>
        </div>
        @endif
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total de Hechos</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ number_format($totalHechos) }}</p>
                </div>
                <div class="p-3 bg-primary-100 dark:bg-primary-900 rounded-full">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Categorías</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ count($estadisticasPorCategoria) }}</p>
                </div>
                <div class="p-3 bg-secondary-100 dark:bg-secondary-900 rounded-full">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Barrios Afectados</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ count($estadisticasPorBarrio) }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Con Ubicación</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ count($datosMapaMarkers) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Mapas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Mapa de Calor --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Mapa de Calor</h3>
            {{-- Datos actualizables (SIN wire:ignore) --}}
            <div id="mapaCalorData" style="display: none;"
                 data-puntos='@json($datosMapaCalor)'></div>
            {{-- Contenedor del mapa (CON wire:ignore) --}}
            <div wire:ignore>
                <div id="mapaCalor" style="height: 400px;" class="rounded-lg"></div>
            </div>
        </div>

        {{-- Mapa de Marcadores --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Mapa de Puntos</h3>
            {{-- Datos actualizables (SIN wire:ignore) --}}
            <div id="mapaMarkersData" style="display: none;"
                 data-markers='@json($datosMapaMarkers)'></div>
            {{-- Contenedor del mapa (CON wire:ignore) --}}
            <div wire:ignore>
                <div id="mapaMarkers" style="height: 400px;" class="rounded-lg"></div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Gráfico por Categoría --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Hechos por Categoría</h3>
            @if(count($estadisticasPorCategoria) > 0)
                {{-- Datos actualizables (SIN wire:ignore) --}}
                <div id="chartCategoriaData" style="display: none;"
                     data-categorias='@json(array_column($estadisticasPorCategoria, "categoria"))'
                     data-valores='@json(array_column($estadisticasPorCategoria, "cantidad"))'></div>
                {{-- Contenedor del gráfico (CON wire:ignore) --}}
                <div wire:ignore>
                    <div id="chartCategoria" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>

        {{-- Gráfico por Barrio --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top 10 Barrios</h3>
            @if(count($estadisticasPorBarrio) > 0)
                {{-- Datos actualizables (SIN wire:ignore) --}}
                <div id="chartBarrioData" style="display: none;"
                     data-barrios='@json(array_column($estadisticasPorBarrio, "barrio"))'
                     data-valores='@json(array_column($estadisticasPorBarrio, "cantidad"))'></div>
                {{-- Contenedor del gráfico (CON wire:ignore) --}}
                <div wire:ignore>
                    <div id="chartBarrio" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>

        {{-- Gráfico por Mes --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Evolución Temporal</h3>
            @if(count($estadisticasPorMes) > 0)
                {{-- Datos actualizables (SIN wire:ignore) --}}
                <div id="chartMesData" style="display: none;"
                     data-meses='@json(array_column($estadisticasPorMes, "mes"))'
                     data-valores='@json(array_column($estadisticasPorMes, "cantidad"))'></div>
                {{-- Contenedor del gráfico (CON wire:ignore) --}}
                <div wire:ignore>
                    <div id="chartMes" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>

        {{-- Gráfico por Acción --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Por Acción</h3>
            @if(count($estadisticasPorAccion) > 0)
                {{-- Datos actualizables (SIN wire:ignore) --}}
                <div id="chartAccionData" style="display: none;"
                     data-acciones='@json(array_column($estadisticasPorAccion, "accion"))'
                     data-valores='@json(array_column($estadisticasPorAccion, "cantidad"))'></div>
                {{-- Contenedor del gráfico (CON wire:ignore) --}}
                <div wire:ignore>
                    <div id="chartAccion" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>

        {{-- Gráfico por Desenlace --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Por Desenlace</h3>
            @if(count($estadisticasPorDesenlace) > 0)
                {{-- Datos actualizables (SIN wire:ignore) --}}
                <div id="chartDesenlaceData" style="display: none;"
                     data-desenlaces='@json(array_column($estadisticasPorDesenlace, "desenlace"))'
                     data-valores='@json(array_column($estadisticasPorDesenlace, "cantidad"))'></div>
                {{-- Contenedor del gráfico (CON wire:ignore) --}}
                <div wire:ignore>
                    <div id="chartDesenlace" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>
    </div>

    {{-- === ANÁLISIS DE INVOLUCRADOS === --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Análisis de Involucrados</h2>
    </div>

    {{-- Gráficos de Tipos de Involucrados --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Tipo de Involucrado 1 --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tipo de Involucrado 1</h3>
            @if(count($estadisticasPorTipoInvolucrado1) > 0)
                <div id="chartTipoInv1Data" style="display: none;"
                     data-tipos='@json(array_column($estadisticasPorTipoInvolucrado1, "tipo"))'
                     data-valores='@json(array_column($estadisticasPorTipoInvolucrado1, "cantidad"))'></div>
                <div wire:ignore>
                    <div id="chartTipoInv1" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>

        {{-- Tipo de Involucrado 2 --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tipo de Involucrado 2</h3>
            @if(count($estadisticasPorTipoInvolucrado2) > 0)
                <div id="chartTipoInv2Data" style="display: none;"
                     data-tipos='@json(array_column($estadisticasPorTipoInvolucrado2, "tipo"))'
                     data-valores='@json(array_column($estadisticasPorTipoInvolucrado2, "cantidad"))'></div>
                <div wire:ignore>
                    <div id="chartTipoInv2" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Gráficos de Sexo de Involucrados --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Sexo de Involucrado 1 --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Sexo de Involucrado 1</h3>
            @if(count($estadisticasPorSexoInvolucrado1) > 0)
                <div id="chartSexoInv1Data" style="display: none;"
                     data-sexos='@json(array_column($estadisticasPorSexoInvolucrado1, "sexo"))'
                     data-valores='@json(array_column($estadisticasPorSexoInvolucrado1, "cantidad"))'></div>
                <div wire:ignore>
                    <div id="chartSexoInv1" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>

        {{-- Sexo de Involucrado 2 --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Sexo de Involucrado 2</h3>
            @if(count($estadisticasPorSexoInvolucrado2) > 0)
                <div id="chartSexoInv2Data" style="display: none;"
                     data-sexos='@json(array_column($estadisticasPorSexoInvolucrado2, "sexo"))'
                     data-valores='@json(array_column($estadisticasPorSexoInvolucrado2, "cantidad"))'></div>
                <div wire:ignore>
                    <div id="chartSexoInv2" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Gráficos de Rango de Edad de Involucrados --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Rango de Edad de Involucrado 1 --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rango de Edad - Involucrado 1</h3>
            @if(count($estadisticasPorRangoEdadInvolucrado1) > 0)
                <div id="chartEdadInv1Data" style="display: none;"
                     data-rangos='@json(array_column($estadisticasPorRangoEdadInvolucrado1, "rango"))'
                     data-valores='@json(array_column($estadisticasPorRangoEdadInvolucrado1, "cantidad"))'></div>
                <div wire:ignore>
                    <div id="chartEdadInv1" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>

        {{-- Rango de Edad de Involucrado 2 --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rango de Edad - Involucrado 2</h3>
            @if(count($estadisticasPorRangoEdadInvolucrado2) > 0)
                <div id="chartEdadInv2Data" style="display: none;"
                     data-rangos='@json(array_column($estadisticasPorRangoEdadInvolucrado2, "rango"))'
                     data-valores='@json(array_column($estadisticasPorRangoEdadInvolucrado2, "cantidad"))'></div>
                <div wire:ignore>
                    <div id="chartEdadInv2" style="min-height: 350px;"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                    <p>No hay datos para mostrar</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabla de Datos y Exportación --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Datos Filtrados</h3>
            <div class="flex gap-2">
                <button wire:click="exportarExcel"
                        class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-md transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
                </button>
                <button wire:click="exportarPDF"
                        class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    PDF
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-primary dark:bg-primary-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Categoría</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Barrio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Zona</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase">Tipo Domicilio</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($hechosFiltrados->take(50) as $hecho)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">#{{ $hecho->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $hecho->fecha_hecho->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $hecho->categoria->nombre ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $hecho->barrio->nombre ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $hecho->zona }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $hecho->tipo_domicilio }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron hechos con los filtros aplicados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($hechosFiltrados->count() > 50)
                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400 text-center">
                    Mostrando primeros 50 de {{ $hechosFiltrados->count() }} registros. Exporta para ver todos.
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Función para cargar Google Maps dinámicamente
        function cargarGoogleMaps(callback) {
            // Si ya está cargado, llamar directamente al callback
            if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                console.log('Google Maps ya está cargado');
                callback();
                return;
            }

            // Si ya hay un script cargándose, esperar a que termine
            if (window.googleMapsLoading) {
                console.log('Google Maps se está cargando, esperando...');
                const interval = setInterval(() => {
                    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                        clearInterval(interval);
                        callback();
                    }
                }, 100);
                return;
            }

            // Marcar que se está cargando
            window.googleMapsLoading = true;

            console.log('Cargando Google Maps con librería places...');
            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&loading=async';
            script.async = true;
            script.defer = true;
            script.onload = () => {
                console.log('Google Maps cargado exitosamente');
                window.googleMapsLoading = false;
                callback();
            };
            script.onerror = () => {
                console.error('Error al cargar Google Maps');
                window.googleMapsLoading = false;
            };
            document.head.appendChild(script);
        }

        // Diagnóstico inicial
        console.log('=== DIAGNÓSTICO DE ESTADÍSTICAS ===');
        console.log('window.ApexCharts:', typeof window.ApexCharts);
        console.log('window.google:', typeof window.google);
        console.log('window.Livewire:', typeof window.Livewire);

        // Variables globales para estadísticas
        if (typeof window.estadisticasData === 'undefined') {
            window.estadisticasData = {
                mapaCalor: null,
                mapaMarkers: null,
                heatmap: null,
                heatmapCircles: [],
                markers: [],
                chartCategoria: null,
                chartBarrio: null,
                chartTemporal: null,
                chartAccion: null,
                chartDesenlace: null
            };
        }

        document.addEventListener('livewire:navigated', function() {
            console.log('Livewire navigated event fired');

            // Solo ejecutar si estamos en la página de estadísticas
            if (!document.getElementById('mapaCalor')) {
                console.log('No estamos en la página de estadísticas, saliendo...');
                return;
            }

            // Limpiar instancias anteriores
            if (window.estadisticasData.heatmap) {
                window.estadisticasData.heatmap.setMap(null);
                window.estadisticasData.heatmap = null;
            }
            if (window.estadisticasData.heatmapCircles) {
                window.estadisticasData.heatmapCircles.forEach(function(circle) {
                    circle.setMap(null);
                });
                window.estadisticasData.heatmapCircles = [];
            }
            if (window.estadisticasData.markers) {
                window.estadisticasData.markers.forEach(function(marker) {
                    marker.setMap(null);
                });
                window.estadisticasData.markers = [];
            }
            window.estadisticasData.mapaCalor = null;
            window.estadisticasData.mapaMarkers = null;

            function inicializarMapas() {
                // Centro en las coordenadas especificadas
                const centro = { lat: -34.65041121106401, lng: -59.43203992015478 };

                // Mapa de Calor
                window.estadisticasData.mapaCalor = new google.maps.Map(document.getElementById('mapaCalor'), {
                    zoom: 10,
                    center: centro,
                    mapTypeId: 'roadmap'
                });

                // Mapa de Marcadores
                window.estadisticasData.mapaMarkers = new google.maps.Map(document.getElementById('mapaMarkers'), {
                    zoom: 10,
                    center: centro,
                    mapTypeId: 'roadmap'
                });

                actualizarMapas();
            }

            function actualizarMapas() {
                console.log('Actualizando mapas...');

                // Leer datos actualizados del DOM desde los elementos Data
                const elementCalorData = document.getElementById('mapaCalorData');
                const elementMarkersData = document.getElementById('mapaMarkersData');

                if (!elementCalorData || !elementMarkersData) {
                    console.log('Elementos de datos de mapa no encontrados');
                    return;
                }

                const datosCalor = JSON.parse(elementCalorData.getAttribute('data-puntos') || '[]');
                const datosMarkers = JSON.parse(elementMarkersData.getAttribute('data-markers') || '[]');

                console.log('Datos calor:', datosCalor.length, 'Markers:', datosMarkers.length);

                // Actualizar Mapa de Calor
                if (datosCalor.length > 0 && window.estadisticasData.mapaCalor) {
                    const heatmapData = datosCalor.map(function(punto) {
                        return new google.maps.LatLng(punto.lat, punto.lng);
                    });

                    // Crear círculos con gradiente de calor en lugar de HeatmapLayer deprecado
                    const bounds = new google.maps.LatLngBounds();

                    // Limpiar heatmap anterior si existe
                    if (window.estadisticasData.heatmapCircles) {
                        window.estadisticasData.heatmapCircles.forEach(function(circle) {
                            circle.setMap(null);
                        });
                    }
                    window.estadisticasData.heatmapCircles = [];

                    // Crear círculos para simular mapa de calor
                    heatmapData.forEach(function(punto) {
                        const circle = new google.maps.Circle({
                            strokeColor: '#FF0000',
                            strokeOpacity: 0.3,
                            strokeWeight: 1,
                            fillColor: '#FF0000',
                            fillOpacity: 0.35,
                            map: window.estadisticasData.mapaCalor,
                            center: punto,
                            radius: 200
                        });
                        window.estadisticasData.heatmapCircles.push(circle);
                        bounds.extend(punto);
                    });

                    // Centrar mapa solo si existe
                    if (window.estadisticasData.mapaCalor) {
                        window.estadisticasData.mapaCalor.fitBounds(bounds);
                    }
                }

                // Actualizar Marcadores
                window.estadisticasData.markers.forEach(function(marker) { marker.setMap(null); });
                window.estadisticasData.markers = [];

                // Función para obtener color por subcategoría
                const coloresPorSubcategoria = {};
                const coloresBase = [
                    '#FF6B6B', // Rojo
                    '#4ECDC4', // Turquesa
                    '#45B7D1', // Azul claro
                    '#FFA07A', // Salmon
                    '#98D8C8', // Verde menta
                    '#F7DC6F', // Amarillo
                    '#BB8FCE', // Púrpura
                    '#85C1E2', // Azul cielo
                    '#F8B739', // Naranja
                    '#52BE80', // Verde
                    '#EC7063', // Rojo coral
                    '#AF7AC5', // Violeta
                ];

                function obtenerColorSubcategoria(subcategoriaId) {
                    if (subcategoriaId === null || subcategoriaId === undefined) {
                        return '#999999'; // Gris para sin subcategoría
                    }

                    if (!coloresPorSubcategoria[subcategoriaId]) {
                        const index = Object.keys(coloresPorSubcategoria).length % coloresBase.length;
                        coloresPorSubcategoria[subcategoriaId] = coloresBase[index];
                    }

                    return coloresPorSubcategoria[subcategoriaId];
                }

                if (datosMarkers.length > 0 && window.estadisticasData.mapaMarkers) {
                    const bounds = new google.maps.LatLngBounds();

                    datosMarkers.forEach(function(punto) {
                        const color = obtenerColorSubcategoria(punto.subcategoria_id);

                        // Crear círculo en lugar de marker
                        const marker = new google.maps.Circle({
                            strokeColor: color,
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: color,
                            fillOpacity: 0.6,
                            map: window.estadisticasData.mapaMarkers,
                            center: { lat: punto.lat, lng: punto.lng },
                            radius: 100,
                            clickable: true
                        });

                        const infowindow = new google.maps.InfoWindow({
                            content: `
                                <div style="padding: 10px;">
                                    <h3 style="font-weight: bold; margin-bottom: 5px;">${punto.titulo}</h3>
                                    <p style="margin: 0;"><strong>${punto.subcategoria}</strong></p>
                                    <p style="margin: 0;">${punto.descripcion}</p>
                                    <p style="margin: 0; color: #666;">Barrio: ${punto.barrio}</p>
                                </div>
                            `
                        });

                        marker.addListener('click', function(event) {
                            infowindow.setPosition(event.latLng);
                            infowindow.open(window.estadisticasData.mapaMarkers);
                        });

                        window.estadisticasData.markers.push(marker);
                        bounds.extend(new google.maps.LatLng(punto.lat, punto.lng));
                    });

                    // Centrar mapa solo si existe
                    if (window.estadisticasData.mapaMarkers) {
                        window.estadisticasData.mapaMarkers.fitBounds(bounds);
                    }
                }
            }

            // Inicializar gráficos
            function actualizarGraficos() {
                console.log('Actualizando gráficos...');
                console.log('ApexCharts disponible:', typeof ApexCharts !== 'undefined');

                // Destruir gráficos existentes y limpiar contenedores
                if (window.estadisticasData.chartCategoria) {
                    window.estadisticasData.chartCategoria.destroy();
                    window.estadisticasData.chartCategoria = null;
                }
                if (window.estadisticasData.chartBarrio) {
                    window.estadisticasData.chartBarrio.destroy();
                    window.estadisticasData.chartBarrio = null;
                }
                if (window.estadisticasData.chartTemporal) {
                    window.estadisticasData.chartTemporal.destroy();
                    window.estadisticasData.chartTemporal = null;
                }
                if (window.estadisticasData.chartAccion) {
                    window.estadisticasData.chartAccion.destroy();
                    window.estadisticasData.chartAccion = null;
                }
                if (window.estadisticasData.chartDesenlace) {
                    window.estadisticasData.chartDesenlace.destroy();
                    window.estadisticasData.chartDesenlace = null;
                }

                // Gráfico por Categoría - Leer datos del DOM desde elemento Data
                const elementCategoriaData = document.querySelector("#chartCategoriaData");
                const elementCategoria = document.querySelector("#chartCategoria");

                if (!elementCategoriaData || !elementCategoria) {
                    console.log('Elementos de chartCategoria no encontrados');
                    return;
                }

                // Limpiar el contenedor del gráfico
                elementCategoria.innerHTML = '';

                const datosCategoria = JSON.parse(elementCategoriaData.getAttribute('data-valores') || '[]');
                const labelsCategoria = JSON.parse(elementCategoriaData.getAttribute('data-categorias') || '[]');

                console.log('Datos categoría:', datosCategoria);
                console.log('Labels categoría:', labelsCategoria);

                const opcionesCategoria = {
                    series: [{
                        name: 'Cantidad',
                        data: datosCategoria
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            horizontal: true,
                            distributed: true
                        }
                    },
                    colors: ['#77BF43', '#144BE9', '#FFA500', '#FF6B6B', '#4ECDC4'],
                    dataLabels: {
                        enabled: true
                    },
                    xaxis: {
                        categories: labelsCategoria
                    },
                    legend: {
                        show: false
                    }
                };

                if (elementCategoria && typeof ApexCharts !== 'undefined' && datosCategoria.length > 0) {
                    window.estadisticasData.chartCategoria = new ApexCharts(elementCategoria, opcionesCategoria);
                    window.estadisticasData.chartCategoria.render();
                    console.log('Gráfico categoría renderizado');
                }

                // Gráfico por Barrio - Leer datos del DOM desde elemento Data
                const elementBarrioData = document.querySelector("#chartBarrioData");
                const elementBarrio = document.querySelector("#chartBarrio");

                if (!elementBarrioData || !elementBarrio) {
                    console.log('Elementos de chartBarrio no encontrados');
                    return;
                }

                // Limpiar el contenedor del gráfico
                elementBarrio.innerHTML = '';

                const datosBarrio = JSON.parse(elementBarrioData.getAttribute('data-valores') || '[]');
                const labelsBarrio = JSON.parse(elementBarrioData.getAttribute('data-barrios') || '[]');

                console.log('Datos barrio:', datosBarrio);
                console.log('Labels barrio:', labelsBarrio);

                const opcionesBarrio = {
                    series: [{
                        name: 'Cantidad',
                        data: datosBarrio
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            horizontal: false
                        }
                    },
                    colors: ['#144BE9'],
                    xaxis: {
                        categories: labelsBarrio,
                        labels: {
                            rotate: -45
                        }
                    }
                };

                if (elementBarrio && typeof ApexCharts !== 'undefined' && datosBarrio.length > 0) {
                    window.estadisticasData.chartBarrio = new ApexCharts(elementBarrio, opcionesBarrio);
                    window.estadisticasData.chartBarrio.render();
                    console.log('Gráfico barrio renderizado');
                }

                // Gráfico Temporal (por Mes) - Leer datos del DOM desde elemento Data
                const elementMesData = document.querySelector("#chartMesData");
                const elementMes = document.querySelector("#chartMes");

                if (!elementMesData || !elementMes) {
                    console.log('Elementos de chartMes no encontrados');
                    return;
                }

                // Limpiar el contenedor del gráfico
                elementMes.innerHTML = '';

                const datosMes = JSON.parse(elementMesData.getAttribute('data-valores') || '[]');
                const labelsMes = JSON.parse(elementMesData.getAttribute('data-meses') || '[]');

                console.log('Datos mes:', datosMes);
                console.log('Labels mes:', labelsMes);

                const opcionesMes = {
                    series: [{
                        name: 'Hechos',
                        data: datosMes
                    }],
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#77BF43'],
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.3,
                        }
                    },
                    xaxis: {
                        categories: labelsMes
                    }
                };

                if (elementMes && typeof ApexCharts !== 'undefined' && datosMes.length > 0) {
                    window.estadisticasData.chartTemporal = new ApexCharts(elementMes, opcionesMes);
                    window.estadisticasData.chartTemporal.render();
                    console.log('Gráfico temporal renderizado');
                }

                // Gráfico por Acción - Leer datos del DOM desde elemento Data
                const elementAccionData = document.querySelector("#chartAccionData");
                const elementAccion = document.querySelector("#chartAccion");

                if (!elementAccionData || !elementAccion) {
                    console.log('Elementos de chartAccion no encontrados');
                } else {
                    // Limpiar el contenedor del gráfico
                    elementAccion.innerHTML = '';

                    const datosAccion = JSON.parse(elementAccionData.getAttribute('data-valores') || '[]');
                    const labelsAccion = JSON.parse(elementAccionData.getAttribute('data-acciones') || '[]');

                    console.log('Datos acción:', datosAccion);
                    console.log('Labels acción:', labelsAccion);

                    const opcionesAccion = {
                        series: datosAccion,
                        chart: {
                            type: 'donut',
                            height: 350
                        },
                        labels: labelsAccion,
                        colors: ['#77BF43', '#144BE9', '#FFA500', '#FF6B6B', '#4ECDC4', '#95E1D3'],
                        legend: {
                            position: 'bottom'
                        },
                        dataLabels: {
                            enabled: true
                        }
                    };

                    if (elementAccion && typeof ApexCharts !== 'undefined' && datosAccion.length > 0) {
                        window.estadisticasData.chartAccion = new ApexCharts(elementAccion, opcionesAccion);
                        window.estadisticasData.chartAccion.render();
                        console.log('Gráfico acción renderizado');
                    }
                }

                // Gráfico por Desenlace - Leer datos del DOM desde elemento Data
                const elementDesenlaceData = document.querySelector("#chartDesenlaceData");
                const elementDesenlace = document.querySelector("#chartDesenlace");

                if (!elementDesenlaceData || !elementDesenlace) {
                    console.log('Elementos de chartDesenlace no encontrados');
                } else {
                    // Limpiar el contenedor del gráfico
                    elementDesenlace.innerHTML = '';

                    const datosDesenlace = JSON.parse(elementDesenlaceData.getAttribute('data-valores') || '[]');
                    const labelsDesenlace = JSON.parse(elementDesenlaceData.getAttribute('data-desenlaces') || '[]');

                    console.log('Datos desenlace:', datosDesenlace);
                    console.log('Labels desenlace:', labelsDesenlace);

                    const opcionesDesenlace = {
                        series: datosDesenlace,
                        chart: {
                            type: 'donut',
                            height: 350
                        },
                        labels: labelsDesenlace,
                        colors: ['#77BF43', '#144BE9', '#FFA500', '#FF6B6B', '#4ECDC4', '#95E1D3'],
                        legend: {
                            position: 'bottom'
                        },
                        dataLabels: {
                            enabled: true
                        }
                    };

                    if (elementDesenlace && typeof ApexCharts !== 'undefined' && datosDesenlace.length > 0) {
                        window.estadisticasData.chartDesenlace = new ApexCharts(elementDesenlace, opcionesDesenlace);
                        window.estadisticasData.chartDesenlace.render();
                        console.log('Gráfico desenlace renderizado');
                    }
                }

                // === NUEVOS GRÁFICOS DE INVOLUCRADOS ===

                // Gráfico Tipo Involucrado 1
                const elemTipoInv1Data = document.querySelector("#chartTipoInv1Data");
                const elemTipoInv1 = document.querySelector("#chartTipoInv1");
                if (elemTipoInv1Data && elemTipoInv1) {
                    elemTipoInv1.innerHTML = '';
                    const datosTipoInv1 = JSON.parse(elemTipoInv1Data.getAttribute('data-valores') || '[]');
                    const labelsTipoInv1 = JSON.parse(elemTipoInv1Data.getAttribute('data-tipos') || '[]');
                    if (datosTipoInv1.length > 0) {
                        new ApexCharts(elemTipoInv1, {
                            series: [{name: 'Cantidad', data: datosTipoInv1}],
                            chart: {type: 'bar', height: 350, toolbar: {show: false}},
                            plotOptions: {bar: {borderRadius: 4, horizontal: true, distributed: true}},
                            colors: ['#77BF43', '#144BE9', '#FFA500', '#FF6B6B', '#4ECDC4'],
                            dataLabels: {enabled: true},
                            xaxis: {categories: labelsTipoInv1},
                            legend: {show: false}
                        }).render();
                    }
                }

                // Gráfico Tipo Involucrado 2
                const elemTipoInv2Data = document.querySelector("#chartTipoInv2Data");
                const elemTipoInv2 = document.querySelector("#chartTipoInv2");
                if (elemTipoInv2Data && elemTipoInv2) {
                    elemTipoInv2.innerHTML = '';
                    const datosTipoInv2 = JSON.parse(elemTipoInv2Data.getAttribute('data-valores') || '[]');
                    const labelsTipoInv2 = JSON.parse(elemTipoInv2Data.getAttribute('data-tipos') || '[]');
                    if (datosTipoInv2.length > 0) {
                        new ApexCharts(elemTipoInv2, {
                            series: [{name: 'Cantidad', data: datosTipoInv2}],
                            chart: {type: 'bar', height: 350, toolbar: {show: false}},
                            plotOptions: {bar: {borderRadius: 4, horizontal: true, distributed: true}},
                            colors: ['#77BF43', '#144BE9', '#FFA500', '#FF6B6B', '#4ECDC4'],
                            dataLabels: {enabled: true},
                            xaxis: {categories: labelsTipoInv2},
                            legend: {show: false}
                        }).render();
                    }
                }

                // Gráfico Sexo Involucrado 1
                const elemSexoInv1Data = document.querySelector("#chartSexoInv1Data");
                const elemSexoInv1 = document.querySelector("#chartSexoInv1");
                if (elemSexoInv1Data && elemSexoInv1) {
                    elemSexoInv1.innerHTML = '';
                    const datosSexoInv1 = JSON.parse(elemSexoInv1Data.getAttribute('data-valores') || '[]');
                    const labelsSexoInv1 = JSON.parse(elemSexoInv1Data.getAttribute('data-sexos') || '[]');
                    if (datosSexoInv1.length > 0) {
                        new ApexCharts(elemSexoInv1, {
                            series: datosSexoInv1,
                            chart: {type: 'pie', height: 350},
                            labels: labelsSexoInv1,
                            colors: ['#144BE9', '#FF6B6B', '#9CA3AF'],
                            legend: {position: 'bottom'},
                            dataLabels: {enabled: true}
                        }).render();
                    }
                }

                // Gráfico Sexo Involucrado 2
                const elemSexoInv2Data = document.querySelector("#chartSexoInv2Data");
                const elemSexoInv2 = document.querySelector("#chartSexoInv2");
                if (elemSexoInv2Data && elemSexoInv2) {
                    elemSexoInv2.innerHTML = '';
                    const datosSexoInv2 = JSON.parse(elemSexoInv2Data.getAttribute('data-valores') || '[]');
                    const labelsSexoInv2 = JSON.parse(elemSexoInv2Data.getAttribute('data-sexos') || '[]');
                    if (datosSexoInv2.length > 0) {
                        new ApexCharts(elemSexoInv2, {
                            series: datosSexoInv2,
                            chart: {type: 'pie', height: 350},
                            labels: labelsSexoInv2,
                            colors: ['#144BE9', '#FF6B6B', '#9CA3AF'],
                            legend: {position: 'bottom'},
                            dataLabels: {enabled: true}
                        }).render();
                    }
                }

                // Gráfico Rango Edad Involucrado 1
                const elemEdadInv1Data = document.querySelector("#chartEdadInv1Data");
                const elemEdadInv1 = document.querySelector("#chartEdadInv1");
                if (elemEdadInv1Data && elemEdadInv1) {
                    elemEdadInv1.innerHTML = '';
                    const datosEdadInv1 = JSON.parse(elemEdadInv1Data.getAttribute('data-valores') || '[]');
                    const labelsEdadInv1 = JSON.parse(elemEdadInv1Data.getAttribute('data-rangos') || '[]');
                    if (datosEdadInv1.length > 0) {
                        new ApexCharts(elemEdadInv1, {
                            series: [{name: 'Cantidad', data: datosEdadInv1}],
                            chart: {type: 'bar', height: 350, toolbar: {show: false}},
                            plotOptions: {bar: {borderRadius: 4, horizontal: false}},
                            colors: ['#77BF43'],
                            xaxis: {categories: labelsEdadInv1, labels: {rotate: -45}},
                            dataLabels: {enabled: true}
                        }).render();
                    }
                }

                // Gráfico Rango Edad Involucrado 2
                const elemEdadInv2Data = document.querySelector("#chartEdadInv2Data");
                const elemEdadInv2 = document.querySelector("#chartEdadInv2");
                if (elemEdadInv2Data && elemEdadInv2) {
                    elemEdadInv2.innerHTML = '';
                    const datosEdadInv2 = JSON.parse(elemEdadInv2Data.getAttribute('data-valores') || '[]');
                    const labelsEdadInv2 = JSON.parse(elemEdadInv2Data.getAttribute('data-rangos') || '[]');
                    if (datosEdadInv2.length > 0) {
                        new ApexCharts(elemEdadInv2, {
                            series: [{name: 'Cantidad', data: datosEdadInv2}],
                            chart: {type: 'bar', height: 350, toolbar: {show: false}},
                            plotOptions: {bar: {borderRadius: 4, horizontal: false}},
                            colors: ['#77BF43'],
                            xaxis: {categories: labelsEdadInv2, labels: {rotate: -45}},
                            dataLabels: {enabled: true}
                        }).render();
                    }
                }

                console.log('Todos los gráficos actualizados (incluidos involucrados)');
            }

            // Inicializar cuando todo esté listo
            function inicializar() {
                console.log('Inicializando dashboard...');
                console.log('Google Maps:', typeof google !== 'undefined');
                console.log('ApexCharts:', typeof ApexCharts !== 'undefined');
                console.log('Livewire:', typeof Livewire !== 'undefined');

                // Cargar Google Maps y luego inicializar mapas
                cargarGoogleMaps(() => {
                    console.log('Google Maps listo, inicializando mapas...');
                    inicializarMapas();
                    actualizarMapas();
                });

                // Inicializar gráficos
                if (typeof ApexCharts !== 'undefined') {
                    actualizarGraficos();
                } else {
                    console.error('ApexCharts no está disponible');
                }
            }

            // Esperar un momento para asegurar que el DOM está listo
            setTimeout(function() {
                inicializar();
            }, 100);

            // Escuchar evento de Livewire para actualizar visualizaciones
            Livewire.on('actualizarEstadisticas', function() {
                console.log('Evento actualizarEstadisticas recibido, actualizando visualizaciones...');
                setTimeout(function() {
                    actualizarMapas();
                    actualizarGraficos();
                }, 100);
            });
        });
    </script>
    @endpush
</div>
