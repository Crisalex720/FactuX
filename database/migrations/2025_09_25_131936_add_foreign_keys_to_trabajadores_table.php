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
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->foreign(['id_ciudad'], 'trabajadores_id_ciudad_fkey')->references(['id_ciudad'])->on('ciudad')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_depart'], 'trabajadores_id_depart_fkey')->references(['id_depart'])->on('departamento')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_pais'], 'trabajadores_id_pais_fkey')->references(['id_pais'])->on('pais')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->dropForeign('trabajadores_id_ciudad_fkey');
            $table->dropForeign('trabajadores_id_depart_fkey');
            $table->dropForeign('trabajadores_id_pais_fkey');
        });
    }
};
