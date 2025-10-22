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
        Schema::create('cliente', function (Blueprint $table) {
            $table->integer('id_cliente')->primary();
            $table->integer('cedula')->default(222222222222)->unique('cliente_cedula_key');
            $table->string('nombre_cl')->default('cliente final');
            $table->integer('celular')->nullable()->default(1234567890);
            $table->string('correo')->nullable()->default('correo@correo.com');
            $table->integer('id_pais')->default(1);
            $table->integer('id_depart');
            $table->integer('id_ciudad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
