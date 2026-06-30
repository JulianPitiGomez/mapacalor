<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GruposReferentesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $grupos = [
            [
                'nombre'                 => 'Grupo Marengo',
                'departamento_id'        => 1,
                'inspector_encargado_id' => 277,
                'inspectores_ids'        => [298, 273, 293, 313, 129, 314, 277, 315],
            ],
            [
                'nombre'                 => 'Grupo López',
                'departamento_id'        => 1,
                'inspector_encargado_id' => 267,
                'inspectores_ids'        => [274, 43, 310, 129, 267, 279, 280, 257],
            ],
        ];

        foreach ($grupos as $grupoData) {
            $inspectoresIds = $grupoData['inspectores_ids'];
            unset($grupoData['inspectores_ids']);

            $grupoExistente = DB::table('grupos')
                ->where('inspector_encargado_id', $grupoData['inspector_encargado_id'])
                ->where('departamento_id', $grupoData['departamento_id'])
                ->first();

            if ($grupoExistente) {
                $grupoId = $grupoExistente->id;
                $this->command->warn("Grupo '{$grupoData['nombre']}' ya existe (ID {$grupoId}), sincronizando inspectores.");
            } else {
                $grupoId = DB::table('grupos')->insertGetId(array_merge($grupoData, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
                $this->command->info("Grupo '{$grupoData['nombre']}' creado con ID {$grupoId}.");
            }

            // Sincronizar pivot: borrar los existentes y reinsertar
            DB::table('grupo_inspector')->where('grupo_id', $grupoId)->delete();

            $pivotData = array_map(fn ($inspectorId) => [
                'grupo_id'     => $grupoId,
                'inspector_id' => $inspectorId,
                'created_at'   => $now,
                'updated_at'   => $now,
            ], $inspectoresIds);

            DB::table('grupo_inspector')->insert($pivotData);

            $this->command->info("  → " . count($inspectoresIds) . " inspectores sincronizados.");
        }
    }
}
