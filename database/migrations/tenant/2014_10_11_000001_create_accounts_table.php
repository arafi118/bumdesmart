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
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('kode')->unique();
            $table->string('nama');
            $table->foreignId('parent_id')->constrained('akun_level3s');
            $table->string('jenis_mutasi');
            $table->string('no_rek_bank')->nullable();
            $table->string('atas_nama_rek')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
