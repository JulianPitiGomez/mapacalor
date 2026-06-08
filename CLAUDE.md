# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Qué es este proyecto

**MapaCalor** ("Observatorio de Seguridad" de la Municipalidad de Mercedes) es una aplicación Laravel 12 para registrar **hechos** (incidentes de seguridad geolocalizados) y visualizarlos como mapa de calor y estadísticas, más un módulo de gestión de **operativos** de inspección. La interfaz está en español; nombres de modelos, rutas, columnas y variables están en español.

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

**Operativos** (`app/Models/Operativo.php`): operativos de inspección con `estado` (`planificado`/`en_curso`/`finalizado`/`cancelado` — ver helpers `getEstadoBadgeClassAttribute`/`getEstadoLabelAttribute`), departamento, inspector referente, y muchos inspectores (pivote `operativo_inspector` con `estado`/`observacion`). Los **grupos** agrupan inspectores reutilizables.

## Patrón de UI: controladores delgados + Livewire

Los controladores **casi no tienen lógica**: solo devuelven vistas Blade (ej. `HechoController` retorna `hechos.index`, `hechos.create`, `hechos.edit`). **Toda la lógica de listados, formularios, filtros y estadísticas vive en componentes Livewire** (`app/Livewire/`), montados dentro de esas vistas:

- `Estadisticas` — el componente central: aplica ~20 filtros sobre `Hecho`, calcula agregados (por categoría, barrio, mes, horario, tipo/sexo/edad de involucrados, acción, desenlace) y arma `datosMapaCalor`/`datosMapaMarkers` para el mapa. Tras filtrar hace `$this->dispatch('actualizarEstadisticas')` y el JS de `resources/views/livewire/estadisticas.blade.php` redibuja el mapa de Google y los charts.
- `HechoForm`/`HechosList`, `OperativoForm`/`OperativosList`, `GrupoForm`/`GruposList`, `UserForm`/`UsersList`, `CategoriasIndex`, `EstadisticasOperativos`.

Catálogos secundarios (subcategorías, tipos involucrados, horarios, acciones, desenlaces) **no tienen vistas propias**: se editan vía AJAX/modal con rutas `store/update/destroy` sueltas en `routes/web.php`.

## Autorización y rutas

- Autenticación con **Laravel Breeze** (Blade). Rutas auth en `routes/auth.php`.
- **Supervisores:** el flag `es_supervisor` (boolean en `users`) gobernado por el middleware `EsSupervisor` (alias `es_supervisor`, registrado en `bootstrap/app.php`). Las rutas de **operativos, grupos, usuarios y estadísticas-operativos** están detrás de `middleware('es_supervisor')` → abortan 403 si el usuario no es supervisor.
- `/` es la landing pública (`welcome.blade.php`); `/estadisticas` es el panel principal (auth + verified). `/dashboard` redirige a `/estadisticas` por compatibilidad.
- **Manejo especial de error 419** (CSRF/sesión expirada) en `bootstrap/app.php`: responde JSON para peticiones AJAX/Livewire y `errors.419` para el resto. Existe también `HandleSessionExpiration` middleware.

## Convenciones

- Código nuevo en **español** para coincidir con el existente (nombres de métodos Livewire como `aplicarFiltros`, `limpiarFiltros`, variables como `$hechosFiltrados`).
- Formatear PHP con **Pint** antes de commitear.
- No commitear `.env`, `.env.production` ni `public/build` (ya en `.gitignore`).
- Hay un archivo `munimer_mapacalor` en la raíz (dump SQL, ~122 KB) y un `reloj.blade.php` suelto en la raíz: no son parte del flujo de la app.
