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
        Schema::create('operativo_inspector', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operativo_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('inspector_id')->comment('Referencia a fa_inspector en munimer_faltas');
            $table->timestamps();

            $table->unique(['operativo_id', 'inspector_id']);
            $table->index('inspector_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operativo_inspector');
    }
};
