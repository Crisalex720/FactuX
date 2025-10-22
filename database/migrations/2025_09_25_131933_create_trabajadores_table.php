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
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->integer('id_trab')->primary();
            $table->integer('cedula')->unique('trabajadores_cedula_key');
            $table->string('nombre');
            $table->string('apellido');
            $table->integer('id_pais');
            $table->integer('id_depart');
            $table->integer('id_ciudad');
            $table->string('cargo');
            $table->text('contraseña');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
