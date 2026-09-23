# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Qué es este proyecto

**MapaCalor** ("Observatorio de Seguridad" de la Municipalidad de Mercedes) es una aplicación Laravel 12 para registrar **hechos** (incidentes de seguridad geolocalizados) y visualizarlos como mapa de calor y estadísticas, más un módulo de gestión de **operativos** de inspección y estadísticas de las **actas** del sistema de faltas. La interfaz está en español; nombres de modelos, rutas, columnas y variables están en español.

- **Stack:** Laravel 12 (PHP 8.2+), Livewire 3, Alpine (vía Livewire), Tailwind CSS 3, Vite 7.
- **Frontend del mapa:** Google Maps JavaScript API (no Leaflet, pese a lo que sugieran dependencias). Requiere `GOOGLE_MAPS_API_KEY` en `.env` (`config/services.php` → `services.google.maps_api_key`).
- **Gráficos:** ApexCharts. **Alertas:** SweetAlert2 (`window.Swal`). Ambos se exponen como globales en `resources/js/app.js`.
- **Exportación:** Excel (HTML-table truco en `Estadisticas::exportarExcel`) y PDF (`barryvdh/laravel-dompdf`).

## Comandos

```bash
composer setup          # instalación completa: deps, .env, key, migrate, npm install, build
composer dev            # corre server + queue + pail (logs) + vite a la vez (concurrently)
composer test           # config:clear + php artisan test (PHPUnit)
php artisan test --filter NombreDelTest   # un solo test
npm run dev             # solo Vite en watch
npm run build           # build de assets para producción
./vendor/bin/pint       # formateo/lint de PHP (Laravel Pint)
php artisan migrate      # migraciones (ver nota sobre doble base de datos)
php artisan db:seed --class=AdminUserSeeder   # crea admin@admin.com / Admin123 (es_supervisor)
```

Entorno local: XAMPP en Windows (`C:\xampp\htdocs\mapacalor`). El proyecto está enlazado al remoto `https://github.com/JulianPitiGomez/mapacalor.git` (rama `main`).

## Arquitectura: doble base de datos (lo más importante)

La app usa **dos conexiones MySQL** definidas en `config/database.php`:

1. **Conexión por defecto** → base `munimer_mapacalor`. Contiene las tablas propias de la app (hechos, categorías, operativos, grupos, users, etc.). Todas las migraciones de `database/migrations/` corren acá. **Aunque `.env.example` trae `DB_CONNECTION=sqlite`, el código asume MySQL** porque hay relaciones que referencian el schema con prefijo literal (`munimer_mapacalor.operativo_inspector`, `munimer_mapacalor.grupo_inspector`); con SQLite esas relaciones fallan. Usar MySQL en local.
2. **`mysql_faltas`** → base **legacy/externa** `munimer_faltas` (charset `utf8mb3`). Es de **solo lectura** desde esta app y provee inspectores y departamentos. Variables `DB_FALTAS_*` en `.env`.

Los modelos `Inspector` (`fa_inspector`) y `Departamento` (`fa_departamento`) viven en `mysql_faltas` (`protected $connection = 'mysql_faltas'`, `$timestamps = false`). Sus tablas pivote con operativos/grupos están en la base principal, por eso las relaciones `belongsToMany` usan el nombre de tabla cualificado con schema. **Al tocar relaciones Operativo↔Inspector o Grupo↔Inspector, respetar el prefijo de base de datos** o se rompen los joins cross-database.

## Dominio y modelos

**Núcleo de "hechos"** (`app/Models/Hecho.php`): cada hecho tiene fecha, lat/lng, `barrio_id`, hasta dos involucrados (tipo/sexo/edad), y pertenece a una cadena de catálogos: `Categoria` → `Subcategoria`, más `TipoInvolucrado`, `Horario`, `Accion`, `Desenlace`. **Esos catálogos secundarios se filtran por `categoria_id`** (subcategorías, tipos, horarios, acciones y desenlaces dependen de la categoría seleccionada) y tienen flag `activo`.

`Barrio` (`app/Models/Barrio.php`) es un catálogo independiente (no depende de la categoría) con su propio CRUD completo (`Route::resource('barrios')`, `BarrioController`) y flag `activo`; los hechos lo referencian por `barrio_id`.

**Etiquetas dinámicas:** `Categoria` tiene una columna `etiquetas` casteada a `array` (JSON). Son campos personalizados por categoría que `HechoForm` renderiza como inputs dinámicos (`$etiquetasCategoria`/`$valoresEtiquetas`); sus valores **no se guardan en columnas propias**, sino que se serializan como texto `nombre -> valor` dentro de `observaciones` del hecho (y se reparsean al editar). Al tocar el campo `observaciones` o las etiquetas, tener presente este formato.

**Operativos** (`app/Models/Operativo.php`): operativos de inspección con `estado` (`planificado`/`en_curso`/`finalizado`/`cancelado` — ver helpers `getEstadoBadgeClassAttribute`/`getEstadoLabelAttribute`), departamento, inspector referente, y muchos inspectores (pivote `operativo_inspector` con `estado`/`observacion`). Un mismo inspector puede ser referente de varios operativos simultáneos. Los **grupos** agrupan inspectores reutilizables. Desde junio de 2026 un inspector puede estar en más de un grupo (se quitó el unique de `grupo_inspector.inspector_id`). `GruposReferentesSeeder` carga los grupos Marengo y López: es idempotente pero **reemplaza su pivote**, y debe correr después de la migración `fix_inspector_encargado_grupo_lopez`.

**Actas (base de faltas, solo lectura):** `fa_acta` (~210k filas) no tiene modelo Eloquent; se consulta con `DB::connection('mysql_faltas')->table('fa_acta')`. Datos no obvios:
- `operativo_id > 0` = acta de un operativo. **`0` o `NULL` = sin operativo** (hay miles con 0), así que no usar `IS NOT NULL`.
- `operativo_id = -1` = **acta simple cargada desde el sistema de actas** (`munimer_faltas`, repo aparte, commit `b78bf78` del 22/09/2026). Es la nomenclatura nueva y **rige solo desde `services.actas.nomenclatura_desde`** (`ACTAS_NOMENCLATURA_DESDE`, default `2026-09-23`): **no hubo backfill**, así que las actas anteriores siguen en `0`/`NULL`. El sistema de actas solo escribe `-1` o el id del operativo, nunca otra cosa; no crea actas de cámaras.
- `preacta_id > 0` = acta de **cámaras** (viene de `fa_preacta`: `FECHA`/`HORA` de captura, `LUGARINFRA`, `CONFIRMADA`; columnas en mayúsculas). Ninguna tabla guarda la ruta del video o la foto de la preacta.
- `estado` → `fa_acta_estado` (1 Iniciada, 2 Activa, 3 Aprobada, 4 Vencida, 5 Baja). Motivos vía `fa_acta_motivo` → `fa_motivo`.
- Las actas no tienen coordenadas: la geolocalización sale del operativo. Por eso el mapa solo muestra actas de operativos.
- Hay fechas basura (años 3000, 9322…): filtrar siempre por rango.
- **No hay índice en `fecha`**: cada agregado recorre la tabla (~150 ms). Joins con `fa_acta_motivo` necesitan `STRAIGHT_JOIN` (sin él MySQL arranca por motivos y tarda ~10 s).
- Fotos de actas de operativos: `fot-<actanro con 10 dígitos>-<001..005>.jpg` en `services.actas.fotos_path`/`fotos_url` (`ACTAS_FOTOS_PATH`/`ACTAS_FOTOS_URL`).

## Patrón de UI: controladores delgados + Livewire

Los controladores **casi no tienen lógica**: solo devuelven vistas Blade (ej. `HechoController` retorna `hechos.index`, `hechos.create`, `hechos.edit`). **Toda la lógica de listados, formularios, filtros y estadísticas vive en componentes Livewire** (`app/Livewire/`), montados dentro de esas vistas:

- `Estadisticas` — el componente central: aplica ~20 filtros sobre `Hecho`, calcula agregados (por categoría, barrio, mes, horario, tipo/sexo/edad de involucrados, acción, desenlace) y arma `datosMapaCalor`/`datosMapaMarkers` para el mapa. Tras filtrar hace `$this->dispatch('actualizarEstadisticas')` y el JS de `resources/views/livewire/estadisticas.blade.php` redibuja el mapa de Google y los charts.
- `EstadisticasOperativos` — actas y registros de control de los operativos del período (detalle, motivos y fotos), más mapa de operativos.
- `EstadisticasActas` — **solo actas simples** (excluye `operativo_id > 0`, que ya están en EstadisticasOperativos) y **solo de los departamentos de seguridad** (`DEPARTAMENTOS_SEGURIDAD = [1, 25]`: DEPTO. TRANSITO y CAMARA). El Observatorio no se ocupa de estacionamiento medido, inspección general, OMIC ni los demás, así que esas actas no entran: el recorte deja ~78 mil de ~170 mil. El filtro se aplica en `actasFiltradas()` y también acota los desplegables de departamento e inspector. Todos los filtros pasan por `actasFiltradas()` (fecha, inspector, departamento, origen, estado, búsqueda). Arma indicadores, gráficos (período, inspector, motivo, hora, día de semana, departamento, estado), sección de cámaras y listado paginado de 15. Los motivos del listado se traen aparte, solo para las actas de la página. Los gráficos se redibujan con el evento `actas-actualizadas` que se dispara en `render()` y escucha un bloque `@script`.
  - **Origen del acta**, tres categorías excluyentes y en este orden (`sqlEsCamara()`/`sqlEsOtro()`): **Cámaras** (`preacta_id > 0`, las carga el COM); **Manuales** (las del sistema de actas, `operativo_id = -1`, más todo el histórico anterior a la fecha de corte, cuando la marca no existía); **Otros** (sin preacta, sin `-1` y posteriores al corte: papel u otra vía). La categoría Otros es defensiva y **solo se muestra si hay al menos un acta**: `$hayOtros` gobierna la opción del filtro, el indicador y la serie de los gráficos, así que mientras esté vacía la pantalla queda igual que antes.
- `HechoForm`/`HechosList`, `OperativoForm`/`OperativosList`, `GrupoForm`/`GruposList`, `UserForm`/`UsersList`, `CategoriasIndex`.

**Notificaciones:** los componentes usan `$this->dispatch('toast', message: ..., type: 'success'|'error')` y el contenedor Alpine de `layouts/app.blade.php` los muestra y oculta solos. No usar `session()->flash()` ni el componente `x-flash-message`, que ya no se usa.

**Paginación:** la vista publicada `resources/views/vendor/livewire/tailwind.blade.php` usa las claves `pagination.*`. Solo existen en `lang/es`, así que con `APP_LOCALE=en` se ve el texto crudo. `tailwind-sin-resumen.blade.php` es la misma vista sin la línea "Mostrando X a Y de Z" (la usa EstadisticasActas).

Catálogos secundarios (subcategorías, tipos involucrados, horarios, acciones, desenlaces) **no tienen vistas propias**: se editan vía AJAX/modal con rutas `store/update/destroy` sueltas en `routes/web.php`.

## Autorización y rutas

- Autenticación con **Laravel Breeze** (Blade). Rutas auth en `routes/auth.php`.
- **Supervisores:** el flag `es_supervisor` (boolean en `users`) gobernado por el middleware `EsSupervisor` (alias `es_supervisor`, registrado en `bootstrap/app.php`). Las rutas de **operativos, grupos, usuarios, estadísticas-operativos y estadísticas-actas** están detrás de `middleware('es_supervisor')` → abortan 403 si el usuario no es supervisor.
- `/` es la landing pública (`welcome.blade.php`); `/estadisticas` es el panel principal (auth + verified). `/dashboard` redirige a `/estadisticas` por compatibilidad.
- **Manejo especial de error 419** (CSRF/sesión expirada) en `bootstrap/app.php`: responde JSON para peticiones AJAX/Livewire y `errors.419` para el resto. Existe también `HandleSessionExpiration` middleware.

## Convenciones

- Código nuevo en **español** para coincidir con el existente (nombres de métodos Livewire como `aplicarFiltros`, `limpiarFiltros`, variables como `$hechosFiltrados`).
- Formatear PHP con **Pint** antes de commitear.
- No commitear `.env`, `.env.production` ni `public/build` (ya en `.gitignore`). Como `public/build` no está en git, **todo cambio de vistas con clases de Tailwind nuevas requiere `npm run build` en el servidor**.
- Deploy a producción: ver `docs/deploy-produccion.md`.
- Hay un archivo `munimer_mapacalor` en la raíz (dump SQL, ~122 KB) y un `reloj.blade.php` suelto en la raíz: no son parte del flujo de la app.
