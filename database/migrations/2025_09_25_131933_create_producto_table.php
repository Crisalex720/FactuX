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
        Schema::create('producto', function (Blueprint $table) {
            $table->integer('id_producto')->primary();
            $table->integer('barcode')->unique('producto_barcode_key');
            $table->string('nombre_prod');
            $table->decimal('cantidad_prod', 20)->default(0);
            $table->decimal('precio_costop', 20)->default(1);
            $table->decimal('precio_ventap', 20)->default(1);
            $table->string('imagen_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};
