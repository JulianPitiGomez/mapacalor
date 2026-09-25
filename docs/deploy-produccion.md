# Deploy a producción

Guía para actualizar el servidor oficial de MapaCalor desde `main` (`https://github.com/JulianPitiGomez/mapacalor.git`).

La probamos en el servidor de prueba en septiembre de 2026, con la solapa **Estadísticas Actas**.

## 1. Antes de actualizar

```bash
cd /ruta/del/proyecto
git status                            # no debería haber archivos modificados a mano
git log -1 --oneline                  # versión instalada hoy
git fetch origin
git log --oneline HEAD..origin/main   # commits que se van a aplicar
php artisan migrate:status            # migraciones pendientes
```

- **Backup de la base `munimer_mapacalor`** (`mysqldump` o phpMyAdmin) antes de migrar.
- La base de faltas (`munimer_faltas`) **no se modifica**: la app solo la lee.

### Migraciones que puede tener pendientes

| Migración | Qué hace |
|---|---|
| `2026_06_30_110016_drop_unique_inspector_id_from_grupo_inspector_table` | Permite que un inspector esté en más de un grupo |
| `2026_06_30_113931_fix_inspector_encargado_grupo_lopez` | **Cambia datos**: pasa el encargado del grupo López de inspector 267 a 28 |

## 2. Actualizar

```bash
php artisan down                      # modo mantenimiento

git pull origin main
php artisan migrate --force           # --force es obligatorio en producción
php artisan optimize:clear            # limpia caché de config, rutas y vistas
php artisan optimize                  # solo si producción usa caché de config/rutas

php artisan up
```

- **No se corre `npm run build` en el servidor** (no hay permisos): `public/build` viene compilado en el repo y llega con el `git pull`. Quien hace cambios de vistas, CSS o JS tiene que correr `npm run build` en su máquina y commitear `public/build`.
- **Primera vez con `public/build` en git:** si el servidor tiene un `public/build` viejo sin versionar, `git pull` falla con *untracked working tree files would be overwritten*. Renombrarlo antes del pull (`mv public/build public/build.viejo`) y borrarlo cuando todo ande.
- **`composer install`** solo hace falta si cambió `composer.lock`. Ante la duda: `composer install --no-dev --optimize-autoloader`.
- **Sin `optimize:clear`** la ruta nueva (`/estadisticas-actas`) da 404 si las rutas estaban cacheadas.

### Grupos referentes (opcional)

Si en producción todavía no están cargados los grupos Marengo y López:

```bash
php artisan db:seed --class=GruposReferentesSeeder --force
```

- Correrlo **después** de `migrate`. El seeder busca cada grupo por inspector encargado + departamento; si se corre antes del fix de López (267 → 28), crea el grupo duplicado.
- Es idempotente, pero **reemplaza los inspectores** de esos dos grupos por la lista del seeder, y se pierden los cambios que se hayan hecho a mano.

## 3. Revisar el `.env` de producción

| Variable | Para qué |
|---|---|
| `DB_FALTAS_HOST`, `DB_FALTAS_PORT`, `DB_FALTAS_DATABASE`, `DB_FALTAS_USERNAME`, `DB_FALTAS_PASSWORD` | Base de faltas **real** (inspectores, departamentos, actas). Estadísticas Operativos y Estadísticas Actas leen de acá |
| `ACTAS_FOTOS_PATH`, `ACTAS_FOTOS_URL` | Carpeta y URL de las fotos de actas (`fot-<nro>-<001..005>.jpg`) que se ven en Estadísticas Operativos |
| `GOOGLE_MAPS_API_KEY` | Mapas |
| `APP_LOCALE=es` | Con `en`, la paginación de los listados muestra `pagination.showing …` sin traducir |
| `APP_DEBUG=false` | Producción |

## 4. Verificación

1. Entrar con un usuario supervisor (`es_supervisor = 1`; sin eso no se ven Operativos, Grupos, Usuarios ni las estadísticas de operativos y actas).
2. **Estadísticas Actas** aparece debajo de Estadísticas Operativos; los gráficos se dibujan y responden a los filtros.
3. **Estadísticas Operativos** sigue mostrando las actas de cada operativo con sus fotos.
4. Si algo se ve desalineado o sin estilos: el `public/build` commiteado no está actualizado (faltó `npm run build` antes del commit) o quedó caché del navegador.

## Rendimiento conocido

`fa_acta` (~210k filas) no tiene índice en `fecha`, así que cada carga de Estadísticas Actas tarda ~2 s (12 consultas de ~150 ms). Un índice `fa_acta(fecha)` lo resolvería, pero la base de faltas es de otro sistema: consultarlo con quien la administra antes de crearlo.
