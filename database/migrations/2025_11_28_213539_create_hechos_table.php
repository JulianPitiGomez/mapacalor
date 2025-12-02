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
        Schema::create('hechos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_hecho');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias');

            // Involucrado 1
            $table->foreignId('tipo_involucrado1_id')->nullable()->constrained('tipos_involucrados');
            $table->enum('sexo_involucrado1', ['Varon', 'Mujer', 'Sin datos'])->default('Sin datos');
            $table->integer('edad_involucrado1')->nullable();

            // Involucrado 2
            $table->foreignId('tipo_involucrado2_id')->nullable()->constrained('tipos_involucrados');
            $table->enum('sexo_involucrado2', ['Varon', 'Mujer', 'Sin datos'])->default('Sin datos');
            $table->integer('edad_involucrado2')->nullable();

            // Horario
            $table->foreignId('horario_id')->nullable()->constrained('horarios');

            // Ubicación geográfica
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();

            // Tipo de domicilio
            $table->enum('tipo_domicilio', ['Vivienda', 'Comercio', 'Via Publica', 'Establecimiento Publico', 'Establecimiento Educativo', 'Sin datos'])->default('Sin datos');

            // Zona
            $table->enum('zona', ['Centro', 'Barrio', 'Campo', 'Sin datos'])->default('Sin datos');

            // Campos adicionales
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hechos');
    }
};
