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
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_pembelian')->unique();
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('user_id')->constrained('users');
            $table->string('jenis_pembayaran');
            $table->decimal('subtotal', 20, 2);

            $table->string('jenis_diskon');
            $table->decimal('jumlah_diskon', 20, 2)->default(0);

            $table->string('jenis_cashback');
            $table->decimal('jumlah_cashback', 20, 2)->default(0);

            $table->decimal('jumlah_pajak', 20, 2)->default(0);

            $table->decimal('total', 20, 2);
            $table->decimal('dibayar', 20, 2);
            $table->decimal('kembalian', 20, 2)->default(0);
            $table->decimal('jumlah_utang', 20, 2)->default(0);

            $table->string('status')->default('COMPLETED');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
