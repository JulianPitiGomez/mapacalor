<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol', 20)->default('normal')->after('es_supervisor');
            $table->json('solapas')->nullable()->after('rol');
        });

        // Los usuarios existentes conservan su rol actual.
        DB::table('users')->where('es_supervisor', true)->update(['rol' => 'supervisor']);
        DB::table('users')->where('es_supervisor', false)->update(['rol' => 'normal']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'solapas']);
        });
    }
};
