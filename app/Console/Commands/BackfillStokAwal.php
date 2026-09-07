<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Owner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillStokAwal extends Command
{
    protected $signature = 'app:backfill-stok-awal
                            {--tenant=* : Specific Tenant ID(s) to process}
                            {--dry-run : Show what would be created without writing}
                            {--force : Also recreate missing movement for products that already have some movements}';

    protected $description = 'Backfill stock_movements "Stok Awal Migrasi" dari products.stok_aktual agar Laporan Stok per periode tidak 0';

    /**
     * Laporan Stok per periode membaca mutasi dari tabel stock_movements.
     * Produk yang stoknya diinput langsung ke products.stok_aktual (import/produk baru/timpa DB)
     * tanpa baris movement akan tampil Stok Awal 0 di laporan.
     * Command ini membuat 1 movement "Stok Awal Migrasi" per produk yang belum punya movement.
     */
    public function handle()
    {
        if (function_exists('tenant') && tenant()) {
            return $this->processCurrentTenant();
        }

        $tenantQuery = Owner::query();
        if ($tenantIds = $this->option('tenant')) {
            $tenantQuery->whereIn('id', $tenantIds);
        }

        $tenants = $tenantQuery->get();
        if ($tenants->isEmpty()) {
            $this->warn('No tenants found.');

            return 0;
        }

        foreach ($tenants as $tenant) {
            $this->line('');
            $this->info("Processing Tenant: {$tenant->nama_usaha} (ID: {$tenant->id})");

            try {
                if (tenancy()->initialized) {
                    tenancy()->end();
                }

                tenancy()->initialize($tenant);
                $this->processCurrentTenant();
            } catch (\Throwable $e) {
                $this->error("Failed processing tenant {$tenant->id}: ".$e->getMessage());
            } finally {
                if (tenancy()->initialized) {
                    tenancy()->end();
                }
            }
        }

        return 0;
    }

    private function processCurrentTenant()
    {
        $isDryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        // Produk yang SUDAH punya movement tidak disentuh (kecuali --force):
        // mutasi historis mereka adalah sumber kebenaran laporan.
        $query = Product::query();

        if (! $force) {
            $query->whereDoesntHave('stockMovements');
        }

        $products = $query->orderBy('id')->get();

        if ($products->isEmpty()) {
            $this->info('Semua produk sudah punya mutasi stok. Tidak ada yang perlu dibackfill.');

            return 0;
        }

        $now = now();
        $migrationDate = $now->copy()->subYears(10); // selalu sebelum periode laporan mana pun
        $batchNo = 'MIGRATION-'.$now->format('Ymd');

        $this->info("Produk tanpa mutasi stok: {$products->count()}");
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $inserted = 0;
        $skipped = 0;

        foreach ($products as $product) {
            $stok = (int) $product->stok_aktual;

            // Produk tanpa stok tidak butuh movement (tidak mengubah laporan).
            if ($stok === 0) {
                $skipped++;
                $bar->advance();
                continue;
            }

            if ($isDryRun) {
                $this->line("  [dry-run] {$product->sku} {$product->nama_produk}: akan dibuat movement Stok Awal {$stok}");
                $inserted++;
                $bar->advance();
                continue;
            }

            DB::transaction(function () use ($product, $stok, $migrationDate, $batchNo, $now, &$inserted) {
                StockMovement::create([
                    'business_id' => $product->business_id,
                    'product_id' => $product->id,
                    'tanggal_perubahan_stok' => $migrationDate,
                    'jenis_perubahan' => 'adjustment',
                    'jumlah_perubahan' => $stok,
                    'reference_id' => 0,
                    'reference_type' => 'migration',
                    'catatan' => 'Stok Awal Migrasi (backfill)',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Samakan dengan alur import produk: buat batch migrasi + HPP
                DB::table('product_batches')->insert([
                    'business_id' => $product->business_id,
                    'product_id' => $product->id,
                    'no_batch' => $batchNo,
                    'tanggal_pembelian' => $migrationDate,
                    'harga_satuan' => $product->harga_beli,
                    'jumlah_awal' => $stok,
                    'jumlah_saat_ini' => $stok,
                    'tanggal_kadaluarsa' => null,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                if ((float) $product->biaya_rata_rata == 0.0 && (float) $product->harga_beli > 0) {
                    $product->biaya_rata_rata = $product->harga_beli;
                    $product->save();
                }
            });

            $inserted++;
            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->info("Selesai. Movement dibuat: {$inserted}, dilewati (stok 0): {$skipped}");

        if ($isDryRun) {
            $this->warn('DRY-RUN: tidak ada data yang ditulis. Jalankan tanpa --dry-run untuk menerapkan.');
        }

        return 0;
    }
}
