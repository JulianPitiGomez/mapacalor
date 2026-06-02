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
        Schema::create('operativos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 255);
            $table->string('lugar', 255);
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->date('fecha');
            $table->time('hora_desde');
            $table->time('hora_hasta');
            $table->enum('estado', ['planificado', 'en_curso', 'finalizado', 'cancelado'])->default('planificado');
            $table->unsignedInteger('departamento_id')->comment('Referencia a fa_departamento en munimer_faltas');
            $table->unsignedInteger('inspector_referente_id')->comment('Referencia a fa_inspector en munimer_faltas');
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->timestamps();

            $table->index('fecha');
            $table->index('estado');
            $table->index('departamento_id');
            $table->index('inspector_referente_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operativos');
    }
};
