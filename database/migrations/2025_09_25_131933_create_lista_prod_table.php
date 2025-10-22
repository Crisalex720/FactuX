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
        Schema::create('lista_prod', function (Blueprint $table) {
            $table->integer('id_lista')->primary();
            $table->integer('id_fact');
            $table->integer('id_producto');
            $table->decimal('cantidad', 20)->default(1);
            $table->string('estado')->default('activa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_prod');
    }
};
