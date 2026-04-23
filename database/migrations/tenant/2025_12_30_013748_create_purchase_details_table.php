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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('purchase_id')->constrained('purchases');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 20, 2);

            $table->string('jenis_diskon');
            $table->decimal('jumlah_diskon', 20, 2)->default(0);

            $table->string('jenis_cashback');
            $table->decimal('jumlah_cashback', 20, 2)->default(0);

            $table->decimal('subtotal', 20, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
