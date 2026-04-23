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
        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('stok_sistem');
            $table->integer('stok_fisik');
            $table->integer('selisih');
            $table->string('jenis_selisih');
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
        Schema::dropIfExists('stock_opname_details');
    }
};
