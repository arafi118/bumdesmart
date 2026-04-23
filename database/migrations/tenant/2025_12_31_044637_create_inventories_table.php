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
        Schema::create('inventories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('payment_id')->constrained('payments');
            $table->string('nama_barang');
            $table->dateTime('tanggal_beli');
            $table->dateTime('tanggal_validasi');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 20, 2);
            $table->integer('umur_ekonomis');
            $table->integer('jenis')->default(1);
            $table->integer('kategori')->default(1);
            $table->enum('status', ['baik', 'rusak', 'hilang', 'jual', 'hapus'])->default('baik');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
