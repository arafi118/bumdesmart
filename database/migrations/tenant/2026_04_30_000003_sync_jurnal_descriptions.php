<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration will sync the 'catatan' field in payments table 
        // with the 'keterangan' field in jurnals table for historical data.
        
        // Since it's a multi-tenant app, this needs to run on tenant databases.
        // The standard tenants:migrate command will handle this if placed in database/migrations/tenant
        
        DB::statement("
            UPDATE payments p
            JOIN jurnals j ON p.transaction_id = j.id
            SET p.catatan = j.keterangan
            WHERE p.jenis_transaksi IN ('jurnal', 'jurnal_umum')
            AND (p.catatan = 'Transaksi Jurnal Umum' OR p.catatan IS NULL OR p.catatan = '')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse as it's a data sync
    }
};
