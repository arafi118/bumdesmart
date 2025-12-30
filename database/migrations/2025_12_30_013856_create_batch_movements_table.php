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
        Schema::create('batch_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('batch_id')->constrained('product_batches');
            $table->foreignId('stock_movement_id')->constrained('stock_movements');
            $table->dateTime('tanggal_perubahan');
            $table->string('jenis_transaksi', 20);
            $table->unsignedBigInteger('transaction_detail_id')->nullable();
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 20, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_movements');
    }
};
