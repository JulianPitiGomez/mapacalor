<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('departamento_id');
            $table->unsignedBigInteger('inspector_encargado_id');
            $table->timestamps();

            $table->index('departamento_id');
            $table->index('inspector_encargado_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
