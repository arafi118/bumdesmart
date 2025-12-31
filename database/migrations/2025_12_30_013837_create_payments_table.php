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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('user_id')->constrained('users');
            $table->string('no_pembayaran')->unique();
            $table->dateTime('tanggal_pembayaran');
            $table->string('jenis_transaksi')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->decimal('total_harga', 20, 2);
            $table->string('metode_pembayaran')->nullable();
            $table->string('no_referensi')->nullable()->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('rekening_debit')->constrained('accounts');
            $table->foreignId('rekening_kredit')->constrained('accounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
