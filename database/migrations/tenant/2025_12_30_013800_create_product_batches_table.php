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
        Schema::create('product_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('purchase_detail_id')->nullable()->constrained('purchase_details');
            $table->string('no_batch');
            $table->dateTime('tanggal_pembelian');
            $table->decimal('harga_satuan', 20, 2);
            $table->integer('jumlah_awal');
            $table->integer('jumlah_saat_ini');
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
