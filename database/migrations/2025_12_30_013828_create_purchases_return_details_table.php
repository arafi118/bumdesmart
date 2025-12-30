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
        Schema::create('purchases_return_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('purchases_return_id')->constrained('purchases_returns')->onDelete('cascade');
            $table->foreignId('purchase_detail_id')->constrained('purchase_details');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 20, 2);
            $table->decimal('sub_total', 20, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases_return_details');
    }
};
