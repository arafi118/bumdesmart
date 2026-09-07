<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use App\Utils\StokUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StokPeriodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('products')) {
            Schema::create('products', function ($table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('business_id')->default(1);
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('brand_id')->nullable();
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->unsignedBigInteger('shelf_id')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('sku')->nullable();
                $table->string('barcode')->nullable();
                $table->string('nama_produk')->nullable();
                $table->decimal('harga_beli', 20, 2)->default(0);
                $table->decimal('harga_jual', 20, 2)->default(0);
                $table->integer('stok_minimal')->default(0);
                $table->integer('stok_aktual')->default(0);
                $table->string('metode_biaya')->default('SYSTEM');
                $table->decimal('biaya_rata_rata', 20, 2)->default(0);
                $table->string('gambar')->nullable();
                $table->tinyInteger('is_active')->default(1);
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function ($table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('business_id')->default(1);
                $table->unsignedBigInteger('product_id');
                $table->dateTime('tanggal_perubahan_stok');
                $table->string('jenis_perubahan', 20)->nullable();
                $table->integer('jumlah_perubahan');
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('reference_type', 20)->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }
    }

    private function buatProduk(int $stokAktual): Product
    {
        return Product::create([
            'business_id' => 1, 'category_id' => 1, 'brand_id' => 1, 'unit_id' => 1,
            'sku' => 'SKU-'.uniqid(), 'nama_produk' => 'Produk Uji',
            'harga_beli' => 10000, 'harga_jual' => 15000,
            'stok_aktual' => $stokAktual, 'biaya_rata_rata' => 10000, 'is_active' => 1,
        ]);
    }

    private function mutasi(Product $p, string $tanggal, int $jumlah, string $jenis, string $ref = 'purchase'): void
    {
        StockMovement::create([
            'business_id' => 1, 'product_id' => $p->id,
            'tanggal_perubahan_stok' => Carbon::parse($tanggal),
            'jenis_perubahan' => $jenis, 'jumlah_perubahan' => $jumlah,
            'reference_id' => 0, 'reference_type' => $ref,
        ]);
    }

    /** Spec: Awal=migrasi, Masuk=pembelian periode, Keluar=penjualan periode. */
    public function test_stok_awal_migrasi_bukan_masuk(): void
    {
        $p = $this->buatProduk(4);
        // Migration 20 Aug +4; penjualan historis 1-8 Aug -4 (pra-sistem, diabaikan)
        $this->mutasi($p, '2026-08-01 00:00:00', -4, 'sale', 'sale');
        $this->mutasi($p, '2026-08-05 00:00:00', -4, 'sale', 'sale');
        $this->mutasi($p, '2026-08-08 00:00:00', 4, 'purchase', 'purchase');
        $this->mutasi($p, '2026-08-20 09:24:17', 4, 'adjustment', 'migration');
        // Pembelian & penjualan September
        $this->mutasi($p, '2026-09-02 00:00:00', 10, 'purchase', 'purchase');
        $this->mutasi($p, '2026-09-04 16:57:45', -2, 'sale', 'sale');

        // Periode September
        $hasil = StokUtil::stokPeriode($p, new Carbon('2026-09-01'), new Carbon('2026-09-30'));
        $this->assertSame(4, $hasil['stok_awal'], 'Stok Awal = migrasi 4');
        $this->assertSame(10, $hasil['masuk'], 'Masuk = pembelian September saja');
        $this->assertSame(2, $hasil['keluar']);
        $this->assertSame(12, $hasil['stok_akhir']);
        $this->assertSame(12, $hasil['stok_awal'] + $hasil['masuk'] - $hasil['keluar']);

        // Periode Juli (sebelum semua): stok migrasi tetap tampil sebagai saldo
        $hasilJuli = StokUtil::stokPeriode($p, new Carbon('2026-07-01'), new Carbon('2026-07-31'));
        $this->assertSame(4, $hasilJuli['stok_awal'], 'Migrasi tetap Stok Awal meski periode sebelum tanggal migrasi');
        $this->assertSame(0, $hasilJuli['masuk']);
        $this->assertSame(0, $hasilJuli['keluar']);
        $this->assertSame(4, $hasilJuli['stok_akhir']);
    }

    /** Kecuali migrasi, mutasi lama (kronologis) dihitung normal. */
    public function test_produk_tanpa_migrasi_kumulatif_normal(): void
    {
        $p = $this->buatProduk(9);
        $this->mutasi($p, '2026-07-31 00:00:00', 10, 'purchase', 'purchase');
        $this->mutasi($p, '2026-08-02 00:00:00', -3, 'sale', 'sale');
        $this->mutasi($p, '2026-09-05 00:00:00', -2, 'sale', 'sale');

        $hasilAgustus = StokUtil::stokPeriode($p, new Carbon('2026-08-01'), new Carbon('2026-08-31'));
        $this->assertSame(10, $hasilAgustus['stok_awal']);
        $this->assertSame(0, $hasilAgustus['masuk']);
        $this->assertSame(3, $hasilAgustus['keluar']);
        $this->assertSame(7, $hasilAgustus['stok_akhir']);

        $hasilJuli = StokUtil::stokPeriode($p, new Carbon('2026-07-01'), new Carbon('2026-07-31'));
        $this->assertSame(0, $hasilJuli['stok_awal']);
        $this->assertSame(10, $hasilJuli['masuk']);
        $this->assertSame(0, $hasilJuli['keluar']);
        $this->assertSame(10, $hasilJuli['stok_akhir']);
    }

    /** Migrasi di tengah riwayat: pra-sistem diabaikan, pasca-sistem dihitung. */
    public function test_migrasi_ditengah_riwayat_tidak_double_count(): void
    {
        $p = $this->buatProduk(12);
        // Riwayat historis import: 10 Aug jual 1 (diabaikan: pra-migrasi)
        $this->mutasi($p, '2026-08-10 00:00:00', -1, 'sale', 'sale');
        // Snapshot migrasi 20 Aug: sisa fisik 6
        $this->mutasi($p, '2026-08-20 09:24:17', 6, 'adjustment', 'migration');
        // Pasca migrasi
        $this->mutasi($p, '2026-08-21 00:00:00', 10, 'purchase', 'purchase');
        $this->mutasi($p, '2026-08-23 00:00:00', -4, 'sale', 'sale');
        $this->mutasi($p, '2026-09-02 00:00:00', -2, 'sale', 'sale');

        // Agustus: awal = migrasi 6, masuk 10, keluar 4 -> akhir 12 (cocok master)
        $hasil = StokUtil::stokPeriode($p, new Carbon('2026-08-01'), new Carbon('2026-08-31'));
        $this->assertSame(6, $hasil['stok_awal']);
        $this->assertSame(10, $hasil['masuk']);
        $this->assertSame(4, $hasil['keluar']);
        $this->assertSame(12, $hasil['stok_akhir']);

        // September: awal = 12, keluar 2 -> akhir 10
        $hasilSep = StokUtil::stokPeriode($p, new Carbon('2026-09-01'), new Carbon('2026-09-30'));
        $this->assertSame(12, $hasilSep['stok_awal']);
        $this->assertSame(2, $hasilSep['keluar']);
        $this->assertSame(10, $hasilSep['stok_akhir']);
    }

    /** Produk tanpa movement sama sekali. */
    public function test_produk_tanpa_movement(): void
    {
        $p = $this->buatProduk(15);
        $hasil = StokUtil::stokPeriode($p, new Carbon('2026-09-01'), new Carbon('2026-09-30'));
        $this->assertSame(0, $hasil['stok_awal']);
        $this->assertSame(0, $hasil['masuk']);
        $this->assertSame(0, $hasil['keluar']);
        $this->assertSame(0, $hasil['stok_akhir']);
    }

    /** Boundary: mutasi tepat tanggal batas masuk periode. */
    public function test_batas_periode_inklusif(): void
    {
        $p = $this->buatProduk(5);
        $this->mutasi($p, '2026-09-01 00:00:00', 5, 'purchase', 'purchase');
        $this->mutasi($p, '2026-09-30 23:59:59', -2, 'sale', 'sale');
        $this->mutasi($p, '2026-10-01 00:00:00', -1, 'sale', 'sale');

        $hasil = StokUtil::stokPeriode($p, new Carbon('2026-09-01'), new Carbon('2026-09-30'));
        $this->assertSame(0, $hasil['stok_awal']);
        $this->assertSame(5, $hasil['masuk']);
        $this->assertSame(2, $hasil['keluar']);
        $this->assertSame(3, $hasil['stok_akhir']);
    }
}
