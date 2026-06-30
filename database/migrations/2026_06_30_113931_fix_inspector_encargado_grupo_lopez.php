<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('grupos')->where('inspector_encargado_id', 267)->update(['inspector_encargado_id' => 28]);
    }

    public function down(): void
    {
        DB::table('grupos')->where('inspector_encargado_id', 28)->update(['inspector_encargado_id' => 267]);
    }
};
