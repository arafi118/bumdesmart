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

        // Tabel minimal untuk uji perhitungan (sqlite in-memory)
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

    private function buatProduk(): Product
    {
        return Product::create([
            'business_id' => 1,
            'category_id' => 1,
            'brand_id' => 1,
            'unit_id' => 1,
            'sku' => 'SKU-'.uniqid(),
            'nama_produk' => 'Produk Uji',
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'biaya_rata_rata' => 10000,
            'is_active' => 1,
        ]);
    }

    private function mutasi(Product $p, string $tanggal, int $jumlah, string $jenis): void
    {
        StockMovement::create([
            'business_id' => 1,
            'product_id' => $p->id,
            'tanggal_perubahan_stok' => Carbon::parse($tanggal),
            'jenis_perubahan' => $jenis,
            'jumlah_perubahan' => $jumlah,
        ]);
    }

    /** Laporan Stok per periode: Awal = migrasi, Masuk = pembelian, Keluar = penjualan. */
    public function test_stok_awal_migrasi_dan_mutasi_periode(): void
    {
        $p = $this->buatProduk();

        // Stok awal migrasi: 2 bulan lalu +100
        $this->mutasi($p, Carbon::now()->subMonths(2)->format('Y-m-d H:i:s'), 100, 'adjustment');
        // Pembelian di periode berjalan: +50
        $this->mutasi($p, Carbon::now()->startOfMonth()->addDays(3)->format('Y-m-d H:i:s'), 50, 'purchase');
        // Penjualan di periode berjalan: -30
        $this->mutasi($p, Carbon::now()->format('Y-m-d H:i:s'), -30, 'sale');

        $hasil = StokUtil::stokPeriode($p, Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());

        $this->assertSame(100, $hasil['stok_awal']);
        $this->assertSame(50, $hasil['masuk']);
        $this->assertSame(30, $hasil['keluar']);
        $this->assertSame(120, $hasil['stok_akhir']);
        $this->assertSame(120, $hasil['stok_awal'] + $hasil['masuk'] - $hasil['keluar']);
    }

    /** Produk yang dibuat lewat import sebelum periode: akhir = awal. */
    public function test_produk_tanpa_mutasi_periode(): void
    {
        $p = $this->buatProduk();
        $this->mutasi($p, Carbon::now()->subMonth()->format('Y-m-d H:i:s'), 40, 'adjustment');

        $hasil = StokUtil::stokPeriode($p, Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());

        $this->assertSame(40, $hasil['stok_awal']);
        $this->assertSame(0, $hasil['masuk']);
        $this->assertSame(0, $hasil['keluar']);
        $this->assertSame(40, $hasil['stok_akhir']);
    }

    /** Mutasi tepat di batas periode tidak dihitung ganda; periode berikutnya lanjut dari akhir sebelumnya. */
    public function test_batas_periode_tidak_ganda_dan_maju_berlanjut(): void
    {
        $p = $this->buatProduk();
        $this->mutasi($p, Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00'), 10, 'purchase');
        $this->mutasi($p, Carbon::now()->endOfMonth()->format('Y-m-d 23:59:59'), -4, 'sale');

        $hasil = StokUtil::stokPeriode($p, Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $this->assertSame(0, $hasil['stok_awal']);
        $this->assertSame(10, $hasil['masuk']);
        $this->assertSame(4, $hasil['keluar']);
        $this->assertSame(6, $hasil['stok_akhir']);

        $hasilBerikut = StokUtil::stokPeriode(
            $p,
            Carbon::now()->addMonth()->startOfMonth(),
            Carbon::now()->addMonth()->endOfMonth()
        );
        $this->assertSame(6, $hasilBerikut['stok_awal']);
        $this->assertSame(6, $hasilBerikut['stok_akhir']);
    }
}
