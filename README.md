# MapaCalor — Observatorio de Seguridad

Aplicación de la Municipalidad de Mercedes para registrar **hechos** (incidentes de seguridad geolocalizados) y verlos como mapa de calor y estadísticas. También gestiona los **operativos** de inspección y muestra estadísticas de las **actas** del sistema de faltas.

Stack: Laravel 12 (PHP 8.2+), Livewire 3, Tailwind CSS 3, Vite 7, Google Maps JavaScript API, ApexCharts.

## Módulos

| Módulo | Ruta | Acceso |
|---|---|---|
| Estadísticas de hechos (mapa de calor, gráficos, exportación Excel/PDF) | `/estadisticas` | Usuarios autenticados |
| Hechos, Categorías, Barrios | `/hechos`, `/categorias`, `/barrios` | Usuarios autenticados |
| Operativos | `/operativos` | Supervisores |
| Estadísticas Operativos (actas de operativos, fotos, mapa) | `/estadisticas-operativos` | Supervisores |
| Estadísticas Actas (actas simples de faltas, cámaras vs. manuales) | `/estadisticas-actas` | Supervisores |
| Grupos de inspectores | `/grupos` | Supervisores |
| Usuarios | `/usuarios` | Supervisores |

Un usuario es supervisor cuando tiene `es_supervisor = 1` en la tabla `users`. Se activa desde **Usuarios**, o directo en la base:

```sql
UPDATE users SET es_supervisor = 1 WHERE email = 'usuario@mercedes.gob.ar';
```

## Bases de datos

La app usa dos conexiones MySQL:

- **`munimer_mapacalor`** (conexión por defecto): tablas propias de la app. Acá corren las migraciones.
- **`munimer_faltas`** (conexión `mysql_faltas`, variables `DB_FALTAS_*`): base del sistema de faltas, **solo lectura**. De acá salen inspectores, departamentos y actas.

SQLite no sirve: algunas relaciones usan nombres de tabla con el schema (`munimer_mapacalor.operativo_inspector`).

## Instalación local

Requisitos: PHP 8.2+, Composer, Node.js, MySQL (en la muni se usa XAMPP en Windows) con acceso a una copia de `munimer_faltas`.

```bash
composer setup      # dependencias, .env, key, migraciones, npm install y build
```

Completar en `.env`: `DB_*` (MySQL), `DB_FALTAS_*`, `GOOGLE_MAPS_API_KEY`, `ACTAS_FOTOS_PATH`/`ACTAS_FOTOS_URL` y `APP_LOCALE=es`.

```bash
php artisan db:seed --class=AdminUserSeeder   # admin@admin.com / Admin123 (supervisor; no usar en producción)
composer dev                                  # servidor + cola + logs + Vite
```

## Desarrollo

```bash
composer test         # tests
./vendor/bin/pint     # formateo de PHP
npm run build         # assets de producción
```

`CLAUDE.md` tiene el detalle de arquitectura y convenciones.

## Deploy

Ver [docs/deploy-produccion.md](docs/deploy-produccion.md).
