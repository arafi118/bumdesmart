<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('balances', function (Blueprint $table) {
            $table->decimal('debit_00', 20, 2)->after('tahun')->default(0);
            $table->decimal('kredit_00', 20, 2)->after('debit_00')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balances', function (Blueprint $table) {
            $table->dropColumn('debit_00');
            $table->dropColumn('kredit_00');
        });
    }
};
