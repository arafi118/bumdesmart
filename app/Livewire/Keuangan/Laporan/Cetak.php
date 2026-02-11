<?php

namespace App\Livewire\Keuangan\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchasesReturn;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalesReturn;
use App\Models\StockOpname;
use App\Models\Supplier;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Cetak extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->all();

        if (! isset($data['laporan']) || ! method_exists($this, $data['laporan'])) {
            abort(404, 'Laporan tidak ditemukan');
        }

        // Share business data ke semua view (untuk kop surat)
        $business = Business::first();
        view()->share('business', $business);

        return $this->{$data['laporan']}($data);
    }

    // =====================================================
    // 1. LAPORAN PENJUALAN HARIAN
    // =====================================================
    public function penjualanHarian(array $data)
    {
        $tahun = $data['tahun'] ?? date('Y');
        $bulan = $data['bulan'] ?? '-';
        $hari = $data['periode'] ?? '-';

        $query = Sale::with(['customer'])
            ->whereYear('tanggal_transaksi', $tahun);

        if ($bulan != '-') {
            $query->whereMonth('tanggal_transaksi', $bulan);
        }

        if ($hari != '-') {
            $query->whereDay('tanggal_transaksi', $hari);
        }

        $sales = $query->orderBy('tanggal_transaksi', 'desc')->get();

        $summary = [
            'total_transactions' => $sales->count(),
            'total_sales' => $sales->sum('total'),
            'avg_transaction' => $sales->count() > 0 ? $sales->avg('total') : 0,
        ];

        $title = 'Laporan Penjualan Harian';
        $periodeParts = [];
        if ($bulan != '-') {
            $periodeParts[] = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM');
        }
        $periodeParts[] = $tahun;
        $subtitle = 'Periode: '.implode(' ', $periodeParts);

        $html = view('livewire.keuangan.pelaporan.penjualan-harian', compact('title', 'subtitle', 'sales', 'summary'))->render();

        return $this->streamPdf($html, 'laporan-penjualan-harian.pdf');
    }

    // =====================================================
    // 2. LAPORAN STOK MINIMUM
    // =====================================================
    public function stokMinimum(array $data)
    {
        $products = Product::with('category')
            ->whereColumn('stok_aktual', '<=', 'stok_minimal')
            ->where('is_active', true)
            ->get()
            ->map(function ($product) {
                $product->kekurangan = $product->stok_minimal - $product->stok_aktual;
                $product->suggested_order = ($product->stok_minimal * 2) - $product->stok_aktual;

                return $product;
            })
            ->sortByDesc('kekurangan');

        $title = 'Laporan Stok Minimum';
        $subtitle = 'Tanggal: '.Carbon::now()->isoFormat('D MMMM Y');

        $html = view('livewire.keuangan.pelaporan.stok-minimum', compact('title', 'subtitle', 'products'))->render();

        return $this->streamPdf($html, 'laporan-stok-minimum.pdf');
    }

    // =====================================================
    // 3. LAPORAN LABA RUGI
    // =====================================================
    public function labaRugi(array $data)
    {
        $tahun = $data['tahun'] ?? date('Y');
        $bulan = $data['bulan'] ?? date('m');

        $query = Sale::with('saleDetails')
            ->whereYear('tanggal_transaksi', $tahun);

        if ($bulan != '-') {
            $query->whereMonth('tanggal_transaksi', $bulan);
        }

        $sales = $query->get();

        $totalRevenue = $sales->sum('total');
        $totalHpp = 0;
        $totalProfit = 0;

        foreach ($sales as $sale) {
            foreach ($sale->saleDetails as $detail) {
                $totalHpp += $detail->hpp * $detail->jumlah;
                $totalProfit += $detail->profit;
            }
        }

        $grossMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        $summary = [
            'total_revenue' => $totalRevenue,
            'total_hpp' => $totalHpp,
            'gross_profit' => $totalProfit,
            'gross_margin' => $grossMargin,
        ];

        $title = 'Laporan Laba Rugi';
        $periodeParts = [];
        if ($bulan != '-') {
            $periodeParts[] = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM');
        }
        $periodeParts[] = $tahun;
        $subtitle = 'Periode: '.implode(' ', $periodeParts);

        $html = view('livewire.keuangan.pelaporan.laba-rugi', compact('title', 'subtitle', 'summary'))->render();

        return $this->streamPdf($html, 'laporan-laba-rugi.pdf');
    }

    // =====================================================
    // 4. LAPORAN PRODUK TERLARIS
    // =====================================================
    public function produkTerlaris(array $data)
    {
        $tahun = $data['tahun'] ?? date('Y');
        $bulan = $data['bulan'] ?? date('m');

        $query = SaleDetail::select(
            'product_id',
            DB::raw('SUM(jumlah) as total_terjual'),
            DB::raw('SUM(subtotal) as total_revenue'),
            DB::raw('SUM(profit) as total_profit')
        )
            ->whereHas('sale', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_transaksi', $tahun);
                if ($bulan != '-') {
                    $q->whereMonth('tanggal_transaksi', $bulan);
                }
            })
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->limit(20)
            ->with('product.category')
            ->get();

        $title = 'Laporan Produk Terlaris';
        $periodeParts = [];
        if ($bulan != '-') {
            $periodeParts[] = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM');
        }
        $periodeParts[] = $tahun;
        $subtitle = 'Periode: '.implode(' ', $periodeParts).' (Top 20)';

        $products = $query;

        $html = view('livewire.keuangan.pelaporan.produk-terlaris', compact('title', 'subtitle', 'products'))->render();

        return $this->streamPdf($html, 'laporan-produk-terlaris.pdf');
    }

    // =====================================================
    // 5. LAPORAN PIUTANG (Customer)
    // =====================================================
    public function piutang(array $data)
    {
        $sales = Sale::with('customer')
            ->where('jumlah_utang', '>', 0)
            ->orderBy('tanggal_transaksi', 'asc')
            ->get();

        $grouped = $sales->groupBy('customer_id')->map(function ($items) {
            return [
                'customer' => $items->first()->customer,
                'total_piutang' => $items->sum('jumlah_utang'),
                'jumlah_invoice' => $items->count(),
                'items' => $items,
            ];
        })->sortByDesc('total_piutang');

        $totalPiutang = $sales->sum('jumlah_utang');

        $title = 'Laporan Piutang (Customer)';
        $subtitle = 'Per Tanggal: '.Carbon::now()->isoFormat('D MMMM Y');

        $html = view('livewire.keuangan.pelaporan.piutang', compact('title', 'subtitle', 'grouped', 'totalPiutang'))->render();

        return $this->streamPdf($html, 'laporan-piutang.pdf');
    }

    // =====================================================
    // 6. LAPORAN HUTANG (Supplier)
    // =====================================================
    public function hutang(array $data)
    {
        $purchases = Purchase::with('supplier')
            ->where('jumlah_utang', '>', 0)
            ->orderBy('tanggal_pembelian', 'asc')
            ->get();

        $grouped = $purchases->groupBy('supplier_id')->map(function ($items) {
            return [
                'supplier' => $items->first()->supplier,
                'total_hutang' => $items->sum('jumlah_utang'),
                'jumlah_po' => $items->count(),
                'items' => $items,
            ];
        })->sortByDesc('total_hutang');

        $totalHutang = $purchases->sum('jumlah_utang');

        $title = 'Laporan Hutang (Supplier)';
        $subtitle = 'Per Tanggal: '.Carbon::now()->isoFormat('D MMMM Y');

        $html = view('livewire.keuangan.pelaporan.hutang', compact('title', 'subtitle', 'grouped', 'totalHutang'))->render();

        return $this->streamPdf($html, 'laporan-hutang.pdf');
    }

    // =====================================================
    // 7. LAPORAN STOK OPNAME
    // =====================================================
    public function stokOpname(array $data)
    {
        $tahun = $data['tahun'] ?? date('Y');
        $bulan = $data['bulan'] ?? date('m');

        $query = StockOpname::with(['details.product', 'user'])
            ->whereYear('tanggal_opname', $tahun);

        if ($bulan != '-') {
            $query->whereMonth('tanggal_opname', $bulan);
        }

        $opnames = $query->orderBy('tanggal_opname', 'desc')->get();

        $title = 'Laporan Stok Opname';
        $periodeParts = [];
        if ($bulan != '-') {
            $periodeParts[] = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM');
        }
        $periodeParts[] = $tahun;
        $subtitle = 'Periode: '.implode(' ', $periodeParts);

        $html = view('livewire.keuangan.pelaporan.stok-opname', compact('title', 'subtitle', 'opnames'))->render();

        return $this->streamPdf($html, 'laporan-stok-opname.pdf');
    }

    // =====================================================
    // 8. LAPORAN PEMBELIAN
    // =====================================================
    public function pembelian(array $data)
    {
        $tahun = $data['tahun'] ?? date('Y');
        $bulan = $data['bulan'] ?? date('m');

        $query = Purchase::with('supplier')
            ->whereYear('tanggal_pembelian', $tahun);

        if ($bulan != '-') {
            $query->whereMonth('tanggal_pembelian', $bulan);
        }

        $purchases = $query->orderBy('tanggal_pembelian', 'desc')->get();

        $summary = [
            'total_po' => $purchases->count(),
            'total_pembelian' => $purchases->sum('total'),
            'total_dibayar' => $purchases->sum('dibayar'),
            'total_hutang' => $purchases->sum('jumlah_utang'),
        ];

        $title = 'Laporan Pembelian';
        $periodeParts = [];
        if ($bulan != '-') {
            $periodeParts[] = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM');
        }
        $periodeParts[] = $tahun;
        $subtitle = 'Periode: '.implode(' ', $periodeParts);

        $html = view('livewire.keuangan.pelaporan.pembelian', compact('title', 'subtitle', 'purchases', 'summary'))->render();

        return $this->streamPdf($html, 'laporan-pembelian.pdf');
    }

    // =====================================================
    // 9. LAPORAN MARGIN & PROFITABILITAS
    // =====================================================
    public function marginProduk(array $data)
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('harga_jual', '>', 0)
            ->get()
            ->map(function ($p) {
                $p->margin_rp = $p->harga_jual - $p->biaya_rata_rata;
                $p->margin_pct = $p->harga_jual > 0 ? (($p->harga_jual - $p->biaya_rata_rata) / $p->harga_jual) * 100 : 0;

                return $p;
            })
            ->sortByDesc('margin_pct');

        $title = 'Laporan Margin & Profitabilitas Produk';
        $subtitle = 'Per Tanggal: '.Carbon::now()->isoFormat('D MMMM Y');

        $html = view('livewire.keuangan.pelaporan.margin-produk', compact('title', 'subtitle', 'products'))->render();

        return $this->streamPdf($html, 'laporan-margin-produk.pdf');
    }

    // =====================================================
    // 10. LAPORAN CUSTOMER TERBAIK
    // =====================================================
    public function customerTerbaik(array $data)
    {
        $tahun = $data['tahun'] ?? date('Y');
        $bulan = $data['bulan'] ?? date('m');

        $query = Sale::select(
            'customer_id',
            DB::raw('COUNT(*) as jumlah_transaksi'),
            DB::raw('SUM(total) as total_belanja'),
            DB::raw('AVG(total) as rata_rata')
        )
            ->whereYear('tanggal_transaksi', $tahun);

        if ($bulan != '-') {
            $query->whereMonth('tanggal_transaksi', $bulan);
        }

        $customers = $query
            ->groupBy('customer_id')
            ->orderByDesc('total_belanja')
            ->limit(20)
            ->with('customer')
            ->get();

        $title = 'Laporan Customer Terbaik';
        $periodeParts = [];
        if ($bulan != '-') {
            $periodeParts[] = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM');
        }
        $periodeParts[] = $tahun;
        $subtitle = 'Periode: '.implode(' ', $periodeParts).' (Top 20)';

        $html = view('livewire.keuangan.pelaporan.customer-terbaik', compact('title', 'subtitle', 'customers'))->render();

        return $this->streamPdf($html, 'laporan-customer-terbaik.pdf');
    }

    // =====================================================
    // 11. LAPORAN INVENTORY TURNOVER
    // =====================================================
    public function inventoryTurnover(array $data)
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stok_aktual', '>', 0)
            ->get()
            ->map(function ($p) {
                $terjual30 = SaleDetail::where('product_id', $p->id)
                    ->whereHas('sale', function ($q) {
                        $q->where('tanggal_transaksi', '>=', Carbon::now()->subDays(30));
                    })
                    ->sum('jumlah');

                $p->terjual_30hari = $terjual30;
                $avgDailySales = $terjual30 / 30;
                $p->days_in_inventory = $avgDailySales > 0 ? round($p->stok_aktual / $avgDailySales) : null;
                $p->turnover_ratio = $p->stok_aktual > 0 && $terjual30 > 0 ? round($terjual30 / $p->stok_aktual, 2) : 0;
                $p->nilai_stok = $p->stok_aktual * $p->biaya_rata_rata;

                return $p;
            })
            ->sortByDesc('turnover_ratio');

        $title = 'Laporan Inventory Turnover';
        $subtitle = '30 Hari Terakhir | Per Tanggal: '.Carbon::now()->isoFormat('D MMMM Y');

        $html = view('livewire.keuangan.pelaporan.inventory-turnover', compact('title', 'subtitle', 'products'))->render();

        return $this->streamPdf($html, 'laporan-inventory-turnover.pdf');
    }

    // =====================================================
    // 12. LAPORAN RETUR
    // =====================================================
    public function retur(array $data)
    {
        $tahun = $data['tahun'] ?? date('Y');
        $bulan = $data['bulan'] ?? date('m');

        $salesReturnQuery = SalesReturn::with(['sale.customer', 'user'])
            ->whereYear('tanggal_return', $tahun);
        $purchaseReturnQuery = PurchasesReturn::with(['purchase.supplier', 'user'])
            ->whereYear('tanggal_return', $tahun);

        if ($bulan != '-') {
            $salesReturnQuery->whereMonth('tanggal_return', $bulan);
            $purchaseReturnQuery->whereMonth('tanggal_return', $bulan);
        }

        $salesReturns = $salesReturnQuery->orderBy('tanggal_return', 'desc')->get();
        $purchaseReturns = $purchaseReturnQuery->orderBy('tanggal_return', 'desc')->get();

        $title = 'Laporan Retur';
        $periodeParts = [];
        if ($bulan != '-') {
            $periodeParts[] = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM');
        }
        $periodeParts[] = $tahun;
        $subtitle = 'Periode: '.implode(' ', $periodeParts);

        $html = view('livewire.keuangan.pelaporan.retur', compact('title', 'subtitle', 'salesReturns', 'purchaseReturns'))->render();

        return $this->streamPdf($html, 'laporan-retur.pdf');
    }

    // =====================================================
    // STREAM PDF
    // =====================================================
    private function streamPdf($html, $filename)
    {
        $options = new Options;
        $options->set('defaultFont', 'sans-serif');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
    }
}
