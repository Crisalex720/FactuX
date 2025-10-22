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
        Schema::table('ciudad', function (Blueprint $table) {
            $table->foreign(['id_depart'], 'ciudad_id_depart_fkey')->references(['id_depart'])->on('departamento')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ciudad', function (Blueprint $table) {
            $table->dropForeign('ciudad_id_depart_fkey');
        });
    }
};
