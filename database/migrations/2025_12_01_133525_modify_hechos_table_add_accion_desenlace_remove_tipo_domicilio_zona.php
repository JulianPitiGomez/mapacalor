<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hechos', function (Blueprint $table) {
            // Agregar nuevas columnas
            $table->foreignId('accion_id')->nullable()->after('horario_id')->constrained('acciones')->onDelete('set null');
            $table->foreignId('desenlace_id')->nullable()->after('accion_id')->constrained('desenlaces')->onDelete('set null');

            // Eliminar columnas antiguas
            $table->dropColumn(['tipo_domicilio', 'zona']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hechos', function (Blueprint $table) {
            // Restaurar columnas antiguas
            $table->string('tipo_domicilio')->nullable();
            $table->string('zona')->nullable();

            // Eliminar nuevas columnas
            $table->dropForeign(['accion_id']);
            $table->dropForeign(['desenlace_id']);
            $table->dropColumn(['accion_id', 'desenlace_id']);
        });
    }
};
