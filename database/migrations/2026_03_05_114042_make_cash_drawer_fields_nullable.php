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
        Schema::table('cash_drawers', function (Blueprint $table) {
            $table->dateTime('tanggal_tutup')->nullable()->change();
            $table->decimal('saldo_akhir', 20, 2)->nullable()->change();
            $table->decimal('saldo_akhir_aplikasi', 20, 2)->nullable()->change();
            $table->decimal('selisih', 20, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_drawers', function (Blueprint $table) {
            $table->dateTime('tanggal_tutup')->nullable(false)->change();
            $table->decimal('saldo_akhir', 20, 2)->nullable(false)->change();
            $table->decimal('saldo_akhir_aplikasi', 20, 2)->nullable(false)->change();
            $table->decimal('selisih', 20, 2)->nullable(false)->change();
        });
    }
};
