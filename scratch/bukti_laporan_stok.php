<?php

/**
 * Bukti end-to-end perhitungan Laporan Stok per periode (semantik ANCHOR).
 * Meniru alur Cetak::laporanStok() tanpa render PDF.
 *
 * Prinsip: Stok Akhir periode berjalan = products.stok_aktual (kenyataan/stok master),
 * periode lampau di-unwind mundur memakai mutasi. Stok Awal diserap agar
 * identitas Awal + Masuk - Keluar = Akhir selalu ketutup.
 */

use App\Models\Product;
use App\Models\StockMovement;
use App\Utils\StokUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

Schema::dropIfExists('stock_movements');
Schema::dropIfExists('products');
Schema::create('products', function ($t) {
    $t->bigIncrements('id');
    $t->unsignedBigInteger('business_id')->default(1);
    $t->unsignedBigInteger('category_id')->nullable();
    $t->unsignedBigInteger('brand_id')->nullable();
    $t->unsignedBigInteger('unit_id')->nullable();
    $t->unsignedBigInteger('shelf_id')->nullable();
    $t->unsignedBigInteger('parent_id')->nullable();
    $t->string('sku')->nullable();
    $t->string('barcode')->nullable();
    $t->string('nama_produk')->nullable();
    $t->decimal('harga_beli', 20, 2)->default(0);
    $t->decimal('harga_jual', 20, 2)->default(0);
    $t->integer('stok_minimal')->default(0);
    $t->integer('stok_aktual')->default(0);
    $t->string('metode_biaya')->default('SYSTEM');
    $t->decimal('biaya_rata_rata', 20, 2)->default(0);
    $t->string('gambar')->nullable();
    $t->tinyInteger('is_active')->default(1);
    $t->softDeletes();
    $t->timestamps();
});
Schema::create('categories', function ($t) {
    $t->bigIncrements('id');
    $t->string('nama_kategori')->nullable();
    $t->timestamps();
});
Schema::create('units', function ($t) {
    $t->bigIncrements('id');
    $t->string('nama_satuan')->nullable();
    $t->timestamps();
});
Schema::create('shelves', function ($t) {
    $t->bigIncrements('id');
    $t->string('nama_rak')->nullable();
    $t->timestamps();
});
Schema::create('stock_movements', function ($t) {
    $t->bigIncrements('id');
    $t->unsignedBigInteger('business_id')->default(1);
    $t->unsignedBigInteger('product_id');
    $t->dateTime('tanggal_perubahan_stok');
    $t->string('jenis_perubahan', 20)->nullable();
    $t->integer('jumlah_perubahan');
    $t->unsignedBigInteger('reference_id')->nullable();
    $t->string('reference_type', 20)->nullable();
    $t->text('catatan')->nullable();
    $t->timestamps();
});

$now = Carbon::now();

function buat(string $nama, string $sku, int $stokAktual): Product
{
    return Product::create([
        'business_id' => 1, 'category_id' => 1, 'brand_id' => 1, 'unit_id' => 1,
        'sku' => $sku, 'nama_produk' => $nama, 'stok_aktual' => $stokAktual,
        'harga_beli' => 10000, 'harga_jual' => 15000, 'biaya_rata_rata' => 10000, 'is_active' => 1,
    ]);
}

function mutasi(int $productId, Carbon $tgl, int $jumlah, string $jenis, string $ref): void
{
    StockMovement::create([
        'business_id' => 1, 'product_id' => $productId,
        'tanggal_perubahan_stok' => $tgl, 'jenis_perubahan' => $jenis,
        'jumlah_perubahan' => $jumlah, 'reference_id' => 0, 'reference_type' => $ref,
    ]);
}

// Skenario: Stok Awal = Stok Awal Migrasi, Masuk = Pembelian periode,
// Keluar = Penjualan periode, Stok Akhir = Awal + Masuk - Keluar = stok master.

// A: migrasi 100, beli 50, jual 30. Master = 120.
$a = buat('Beras Premium', 'BR-001', 120);
mutasi($a->id, $now->copy()->subMonths(2), 100, 'adjustment', 'migration');
mutasi($a->id, $now->copy()->startOfMonth()->addDays(3), 50, 'purchase', 'purchase');
mutasi($a->id, $now->copy()->subDays(1), -30, 'sale', 'sale');

// B: migrasi 40, tanpa mutasi periode. Master = 40.
$b = buat('Gula Pasir', 'GL-002', 40);
mutasi($b->id, $now->copy()->subMonth(), 40, 'adjustment', 'migration');

// C: produk BARU (beli pertama kali periode ini). Master = 12.
$c = buat('Minyak Goreng', 'MY-003', 12);
mutasi($c->id, $now->copy()->startOfMonth()->addDays(5), 20, 'purchase', 'purchase');
mutasi($c->id, $now->copy()->subDays(2), -8, 'sale', 'sale');

// D: migrasi 25, retur beli -5, penyesuaian +10. Master = 30.
$d = buat('Tepung Terigu', 'TP-004', 30);
mutasi($d->id, $now->copy()->subMonths(3), 25, 'adjustment', 'migration');
mutasi($d->id, $now->copy()->subDays(4), -5, 'purchase_retur', 'purchases_return');
mutasi($d->id, $now->copy()->subDays(1), 10, 'stock_adjustment', 'stock_adjustment');

// E: DRIFT — import produk dijalankan ulang, master dikoreksi ke 100,
//    tapi riwayat mutasi totalnya 125 (100 + 50 - 30 + dobel +5).
$e = buat('Kecap Manis', 'KC-005', 100);
mutasi($e->id, $now->copy()->subMonths(2), 100, 'adjustment', 'migration');
mutasi($e->id, $now->copy()->startOfMonth()->addDays(3), 50, 'purchase', 'purchase');
mutasi($e->id, $now->copy()->subDays(1), -30, 'sale', 'sale');
mutasi($e->id, $now->copy()->subDays(1), 5, 'purchase', 'purchase'); // dobel dari import ulang

$startDate = $now->copy()->startOfMonth();
$endDate = $now->copy()->endOfMonth();

$products = Product::with(['category', 'unit', 'shelf'])
    ->where('business_id', 1)
    ->where('is_active', true)
    ->orderBy('nama_produk')->get()
    ->map(function ($p) use ($startDate, $endDate) {
        $stok = StokUtil::stokPeriode($p, $startDate, $endDate);
        $p->stok_masuk = $stok['masuk'];
        $p->stok_keluar = $stok['keluar'];
        $p->stok_awal_periode = $stok['stok_awal'];
        $p->stok_akhir = $stok['stok_akhir'];
        $p->nilai_stok = $p->stok_akhir * $p->biaya_rata_rata;

        return $p;
    });

echo "LAPORAN STOK (PER PERIODE) — ".$startDate->isoFormat('MMMM Y')." (semantik anchor ke stok master)\n";
echo str_repeat('-', 100)."\n";
printf("%-16s %10s %8s %8s %10s %12s  %s\n", 'Produk', 'Stok Awal', 'Masuk', 'Keluar', 'Stok Akhir', 'Nilai Stok', 'Master');
echo str_repeat('-', 100)."\n";
foreach ($products as $p) {
    printf("%-16s %10d %8d %8d %10d %12s  %d%s\n",
        $p->nama_produk, $p->stok_awal_periode, $p->stok_masuk, $p->stok_keluar,
        $p->stok_akhir, number_format($p->nilai_stok, 0, ',', '.'), $p->stok_aktual,
        $p->stok_akhir === (int) $p->stok_aktual ? ' ✓' : ' ✗');
}
echo str_repeat('-', 100)."\n";
printf("%-16s %10d %8d %8d %10d\n", 'TOTAL',
    $products->sum('stok_awal_periode'), $products->sum('stok_masuk'),
    $products->sum('stok_keluar'), $products->sum('stok_akhir'));

$ok = true;
foreach ($products as $p) {
    if ($p->stok_akhir !== $p->stok_awal_periode + $p->stok_masuk - $p->stok_keluar) {
        $ok = false;
        echo "[FAIL identitas] {$p->nama_produk}\n";
    }
    if ($p->stok_akhir !== (int) $p->stok_aktual) {
        $ok = false;
        echo "[FAIL anchor] {$p->nama_produk}: laporan {$p->stok_akhir} != master {$p->stok_aktual}\n";
    }
}
echo $ok
    ? "\nVALIDASI OK: Stok Akhir = Stok Awal + Masuk - Keluar DAN = stok master untuk semua baris\n"
    : "\nVALIDASI GAGAL\n";
exit($ok ? 0 : 1);
