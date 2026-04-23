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
        Schema::create('stock_adjustment_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('jumlah');
            $table->string('jenis');
            $table->decimal('harga_satuan', 20, 2);
            $table->decimal('total_harga', 20, 2);
            $table->text('alasan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_details');
    }
};
