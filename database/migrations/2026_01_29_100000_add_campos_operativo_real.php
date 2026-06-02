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
        Schema::table('operativos', function (Blueprint $table) {
            $table->time('hora_apertura_real')->nullable()->after('hora_hasta');
            $table->time('hora_cierre_real')->nullable()->after('hora_apertura_real');
            $table->text('acompanamiento_policial')->nullable()->after('hora_cierre_real');
        });

        Schema::table('operativo_inspector', function (Blueprint $table) {
            $table->enum('estado', ['presente', 'ausente'])->nullable()->after('inspector_id');
            $table->text('observacion')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operativos', function (Blueprint $table) {
            $table->dropColumn(['hora_apertura_real', 'hora_cierre_real', 'acompanamiento_policial']);
        });

        Schema::table('operativo_inspector', function (Blueprint $table) {
            $table->dropColumn(['estado', 'observacion']);
        });
    }
};
