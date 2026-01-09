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
        Schema::create('cash_drawers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('tanggal_buka');
            $table->dateTime('tanggal_tutup');
            $table->decimal('saldo_awal', 20, 2);
            $table->decimal('saldo_akhir', 20, 2);
            $table->decimal('saldo_akhir_aplikasi', 20, 2);
            $table->decimal('selisih', 20, 2);
            $table->text('catatan')->nullable();
            $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_drawers');
    }
};
