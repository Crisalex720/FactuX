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
        Schema::table('lista_prod', function (Blueprint $table) {
            $table->timestamp('fecha_producto')->nullable()->after('cantidad');
            $table->timestamps(); // Agrega created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lista_prod', function (Blueprint $table) {
            $table->dropColumn(['fecha_producto', 'created_at', 'updated_at']);
        });
    }
};
