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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('brand_id')->constrained('brands');
            $table->foreignId('unit_id')->constrained('units');
            $table->string('sku');
            $table->string('nama_produk');
            $table->decimal('harga_beli', 20, 2);
            $table->decimal('harga_jual', 20, 2);
            $table->integer('stok_minimal')->default(0);
            $table->integer('stok_aktual')->default(0);
            $table->string('metode_biaya')->default('SYSTEM');
            $table->decimal('biaya_rata_rata', 20, 2)->default(0);
            $table->string('gambar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
