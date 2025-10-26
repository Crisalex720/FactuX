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
        Schema::table('producto', function (Blueprint $table) {
            $table->decimal('iva_porcentaje', 5, 2)->default(0)->after('precio_ventap')->comment('Porcentaje de IVA del producto (ej: 19.00 para 19%)');
            $table->decimal('valor_iva', 10, 2)->default(0)->after('iva_porcentaje')->comment('Valor del IVA calculado automáticamente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn(['iva_porcentaje', 'valor_iva']);
        });
    }
};
