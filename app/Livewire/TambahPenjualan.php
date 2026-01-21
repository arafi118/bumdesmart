<?php

namespace App\Livewire;

use App\Models\BatchMovement;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductPrice;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Utils\PaymentUtil;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class TambahPenjualan extends Component
{
    public $title;

    public $businessId;

    // View States (Minimal, strictly for initial render/hydration)
    public $nomorPenjualan;

    public $tanggalPenjualan;

    public function mount()
    {
        $this->title = 'Tambah Penjualan';
        $this->businessId = auth()->user()->business_id;
        $this->tanggalPenjualan = date('Y-m-d');
    }

    public function loadCustomers($query, $offset = 0)
    {
        $perPage = 20;
        $customers = Customer::select('id', 'nama_pelanggan', 'kode_pelanggan')
            ->where('business_id', $this->businessId)
            ->where('nama_pelanggan', 'LIKE', "%{$query}%")
            ->orWhere('kode_pelanggan', 'LIKE', "%{$query}%")
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return ['data' => $customers];
    }

    public function loadSearchProducts($query, $customerId = null)
    {
        // 1. Get Customer Group info if Customer selected
        $customerGroup = null;
        if ($customerId) {
            $customer = Customer::with('customerGroup')->find($customerId);
            $customerGroup = $customer->customerGroup ?? null;
        }

        // 2. Search Products (Only with stock > 0)
        $products = Product::where('business_id', $this->businessId)
            ->where('is_active', true)
            ->where('stok_aktual', '>', 0) // Only products with stock
            ->where(function ($q) use ($query) {
                $q->where('nama_produk', 'LIKE', "%{$query}%")
                    ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->take(20)
            ->get();

        $results = [];
        foreach ($products as $p) {
            // Default Price
            $finalPrice = $p->harga_jual;
            $promoLabel = null;

            // 3. Apply Pricing Logic
            if ($customerGroup) {
                // A. Check ProductPrice (Special Price)
                $specialPrice = ProductPrice::where('product_id', $p->id)
                    ->where('customer_group_id', $customerGroup->id)
                    // ->whereDate('start_date', '<=', now()) ... (if dates implemented)
                    ->first();

                if ($specialPrice) {
                    $finalPrice = $specialPrice->harga_spesial;
                    $promoLabel = 'Harga Spesial Member';
                }
                // B. Check Group Discount %
                elseif ($customerGroup->diskon_persen > 0) {
                    $discAmount = ($p->harga_jual * $customerGroup->diskon_persen) / 100;
                    $finalPrice = max(0, $p->harga_jual - $discAmount);
                    $promoLabel = 'Diskon Member '.($customerGroup->diskon_persen + 0).'%';
                }
            }

            // Check Stock status (Simple check)
            $stockInfo = 'Stok: '.$p->stok_aktual;

            $results[] = [
                'id' => $p->id,
                'nama_produk' => $p->nama_produk,
                'sku' => $p->sku,
                'gambar' => $p->gambar,
                'harga_jual' => $finalPrice, // This is the 'System Price'
                'promo_label' => $promoLabel,
                'batch_info' => $stockInfo,
                'original_price' => $p->harga_jual,
                'stok_tersedia' => $p->stok_aktual, // For validation
            ];
        }

        return ['data' => $results];
    }

    #[On('saveAll')]
    public function saveAll($data)
    {
        if (empty($data['products'])) {
            $this->dispatch('alert', type: 'error', message: 'Keranjang kosong');

            return;
        }

        // Validate stock before processing
        foreach ($data['products'] as $item) {
            $product = Product::find($item['id']);
            if (! $product) {
                $this->dispatch('alert', type: 'error', message: 'Produk tidak ditemukan');

                return;
            }

            if ($item['jumlah_jual'] > $product->stok_aktual) {
                $this->dispatch('alert', type: 'error', message: "Stok {$product->nama_produk} tidak mencukupi! Tersedia: {$product->stok_aktual}");

                return;
            }
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $nomorPenjualan = 'INV-'.time(); // Or generate from generic util
            $tgl = $data['tanggalPenjualan'] ?? date('Y-m-d');

            // Payment Logic
            $pay = $this->parseNumber($data['bayar']);
            $grandTotal = $this->parseNumber($data['grandTotal']);
            $jenisPembayaran = $data['jenisPembayaran']; // cash/credit
            $metodeBayar = $data['metodeBayar'];
            $status = 'completed';

            if ($pay < $grandTotal) {
                $status = 'partial';
                $jenisPembayaran = 'credit'; // Force credit if underpaid
            }

            // 1. Create Sale Header
            $sale = Sale::create([
                'business_id' => $this->businessId,
                'customer_id' => $data['customer'] ?: null,
                'user_id' => $user->id,
                'no_invoice' => $nomorPenjualan,
                'tanggal_transaksi' => $tgl,
                'jenis_pembayaran' => $jenisPembayaran, // cash/credit
                'subtotal' => $this->parseNumber($data['subtotal']),
                'jenis_diskon' => $data['globalDiskon']['jenis'],
                'jumlah_diskon' => $this->parseNumber($data['globalDiskon']['jumlah']),
                'jenis_cashback' => $data['globalCashback']['jenis'],
                'jumlah_cashback' => $this->parseNumber($data['globalCashback']['jumlah']),
                'jumlah_pajak' => 0,
                'total' => $grandTotal,
                'dibayar' => $pay,
                'kembalian' => $this->parseNumber($data['kembalian']),
                'jumlah_utang' => max(0, $grandTotal - $pay),
                'status' => $status, // COMPLETED/PARTIAL
                'keterangan' => $data['catatan'] ?? '',
            ]);

            $allBatchMovements = []; // Collect all batch movements for bulk insert
            $stockMovements = [];
            $timestamp = now();

            foreach ($data['products'] as $item) {
                $qty = $item['jumlah_jual'];
                $productId = $item['id'];

                // --- FIFO LOGIC START ---
                $needed = $qty;
                $totalHpp = 0;
                $currentProductBatchMovements = []; // Temporary storage for this product's batches

                // Get Available Batches (Oldest First)
                $batches = ProductBatch::where('product_id', $productId)
                    ->where('status', 'ACTIVE')
                    ->where('jumlah_saat_ini', '>', 0)
                    ->orderBy('tanggal_pembelian', 'asc') // FIFO
                    ->lockForUpdate() // Prevent race conditions
                    ->get();

                foreach ($batches as $batch) {
                    if ($needed <= 0) {
                        break;
                    }

                    $take = min($needed, $batch->jumlah_saat_ini);

                    // Deduct Batch
                    $batch->jumlah_saat_ini -= $take;
                    if ($batch->jumlah_saat_ini == 0) {
                        $batch->status = 'DEPLETED';
                    }
                    $batch->save();

                    // Calculate Cost
                    $totalHpp += ($take * $batch->harga_satuan);

                    // Store batch movement data (will be linked after detail creation)
                    $currentProductBatchMovements[] = [
                        'batch_id' => $batch->id,
                        'qty_taken' => $take,
                        'cost' => $batch->harga_satuan,
                    ];

                    $needed -= $take;
                }

                // Handle Overflow (If stock exists but no batch, or shortage)
                if ($needed > 0) {
                    // Fallback cost: Product's current 'harga_beli' (Master Data)
                    $productMaster = Product::find($productId);
                    $fallbackCost = $productMaster->harga_beli ?? 0;
                    $totalHpp += ($needed * $fallbackCost);
                }

                $avgHpp = ($qty > 0) ? ($totalHpp / $qty) : 0;
                // --- FIFO LOGIC END ---

                // 2. Create Sale Detail
                $detail = $sale->details()->create([
                    'product_id' => $productId,
                    'jumlah' => $qty,
                    'harga_satuan' => $this->parseNumber($item['harga_jual']),
                    'jenis_diskon' => $item['diskon']['jenis'] ?? 'nominal',
                    'jumlah_diskon' => $this->parseNumber($item['diskon']['jumlah'] ?? 0),
                    'jenis_cashback' => 'nominal',
                    'jumlah_cashback' => 0,
                    'subtotal' => $this->parseNumber($item['subtotal']),
                    'hpp' => $avgHpp,
                    'profit' => $this->parseNumber($item['subtotal']) - $totalHpp,
                ]);

                // 3. Prepare Batch Movements for bulk insert (now we have detail ID)
                foreach ($currentProductBatchMovements as $bm) {
                    $allBatchMovements[] = [
                        'product_id' => $productId,
                        'batch_id' => $bm['batch_id'],
                        'qty_taken' => $bm['qty_taken'],
                        'cost' => $bm['cost'],
                        'detail_id' => $detail->id,
                    ];
                }

                // 4. Create Stock Movement immediately to get ID
                $stockMovement = StockMovement::create([
                    'business_id' => $this->businessId,
                    'product_id' => $productId,
                    'tanggal_perubahan_stok' => $tgl,
                    'jenis_perubahan' => 'sale',
                    'jumlah_perubahan' => -$qty,
                    'reference_id' => $sale->id,
                    'reference_type' => 'sale',
                    'catatan' => 'Penjualan '.$nomorPenjualan,
                ]);

                // 5. Link batch movements to stock movement
                foreach ($allBatchMovements as &$bm) {
                    if ($bm['product_id'] === $productId) {
                        $bm['stock_movement_id'] = $stockMovement->id;
                        $bm['business_id'] = $this->businessId;
                        $bm['jenis_transaksi'] = 'sale';
                        $bm['transaction_detail_id'] = $bm['detail_id'];
                        $bm['jumlah'] = $bm['qty_taken'];
                        $bm['harga_satuan'] = $bm['cost'];
                        $bm['tanggal_perubahan'] = $tgl;
                        $bm['created_at'] = $timestamp;
                        $bm['updated_at'] = $timestamp;

                        // Remove temporary keys
                        unset($bm['product_id'], $bm['qty_taken'], $bm['cost'], $bm['detail_id']);
                    }
                }

                // 6. Update Product Stock Master
                Product::where('id', $productId)->decrement('stok_aktual', $qty);
            }

            // Bulk Insert Batch Movements (Performance Optimization)
            if (! empty($allBatchMovements)) {
                BatchMovement::insert($allBatchMovements);
            }

            // 6. Create Payment Records (Double-Entry Accounting) - OPTIMIZED
            $kodeRekening = PaymentUtil::ambilRekening('sales', $jenisPembayaran, $metodeBayar, $data['noRekening']);

            // Fetch all SaleDetails in one query (OPTIMIZATION)
            $details = \App\Models\SaleDetail::where('sale_id', $sale->id)
                ->get()
                ->keyBy('product_id');

            // Calculate totals for accounting (GLOBAL ONLY)
            $totalHppAll = 0;
            $totalDiskonAll = $this->parseNumber($data['globalDiskon']['jumlah']);
            $totalCashbackAll = $this->parseNumber($data['globalCashback']['jumlah']);

            foreach ($data['products'] as $item) {
                $detail = $details[$item['id']] ?? null;
                if ($detail) {
                    $totalHppAll += $detail->hpp * $detail->jumlah;
                }
            }

            $totalProfit = $pay - $totalHppAll; // Laba Kotor = Penerimaan - HPP

            // Collect all payment data for bulk insert (OPTIMIZATION)
            $payments = [];
            $timestamp = now();

            // 6a. HPP Component (Cost Recovery from Sales)
            if ($totalHppAll > 0) {
                $payments[] = [
                    'business_id' => $this->businessId,
                    'user_id' => $user->id,
                    'no_pembayaran' => $nomorPenjualan.'-HPP',
                    'tanggal_pembayaran' => $tgl,
                    'jenis_transaksi' => 'sale_hpp',
                    'transaction_id' => $sale->id,
                    'total_harga' => $totalHppAll,
                    'metode_pembayaran' => $metodeBayar,
                    'no_referensi' => $data['noRekening'] ?? null,
                    'catatan' => 'HPP dari Penjualan',
                    'rekening_debit' => $kodeRekening['sales']['rekening_debit'],
                    'rekening_kredit' => $kodeRekening['sales']['rekening_kredit'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            // 6b. Profit Component (Gross Profit from Sales)
            if ($totalProfit > 0) {
                $payments[] = [
                    'business_id' => $this->businessId,
                    'user_id' => $user->id,
                    'no_pembayaran' => $nomorPenjualan.'-PROFIT',
                    'tanggal_pembayaran' => $tgl,
                    'jenis_transaksi' => 'sale_profit',
                    'transaction_id' => $sale->id,
                    'total_harga' => $totalProfit,
                    'metode_pembayaran' => $metodeBayar,
                    'no_referensi' => $data['noRekening'] ?? null,
                    'catatan' => 'Laba Kotor Penjualan',
                    'rekening_debit' => $kodeRekening['laba']['rekening_debit'],
                    'rekening_kredit' => $kodeRekening['laba']['rekening_kredit'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            // 6c. Sales Discount Entry
            if ($totalDiskonAll > 0) {
                $payments[] = [
                    'business_id' => $this->businessId,
                    'user_id' => $user->id,
                    'no_pembayaran' => $nomorPenjualan.'-DISC',
                    'tanggal_pembayaran' => $tgl,
                    'jenis_transaksi' => 'sale_discount',
                    'transaction_id' => $sale->id,
                    'total_harga' => $totalDiskonAll,
                    'metode_pembayaran' => 'internal',
                    'catatan' => 'Diskon Penjualan',
                    'rekening_debit' => $kodeRekening['sales-diskon']['rekening_debit'],
                    'rekening_kredit' => $kodeRekening['sales-diskon']['rekening_kredit'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            // 6d. Cashback Entry (Marketing Expense)
            if ($totalCashbackAll > 0) {
                $payments[] = [
                    'business_id' => $this->businessId,
                    'user_id' => $user->id,
                    'no_pembayaran' => $nomorPenjualan.'-CSHBK',
                    'tanggal_pembayaran' => $tgl,
                    'jenis_transaksi' => 'sale_cashback',
                    'transaction_id' => $sale->id,
                    'total_harga' => $totalCashbackAll,
                    'metode_pembayaran' => 'internal',
                    'catatan' => 'Cashback Penjualan',
                    'rekening_debit' => $kodeRekening['sales-cashback']['rekening_debit'],
                    'rekening_kredit' => $kodeRekening['sales-cashback']['rekening_kredit'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            // Bulk insert all payments (OPTIMIZATION: 4 queries → 1 query)
            if (! empty($payments)) {
                \App\Models\Payment::insert($payments);
            }

            DB::commit();
            $this->dispatch('alert', type: 'success', message: 'Penjualan berhasil disimpan');
            $this->dispatch('reset-form');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: 'Error: '.$e->getMessage());
        }
    }

    private function parseNumber($val)
    {
        return (float) str_replace(',', '', $val);
    }

    public function render()
    {
        return view('livewire.tambah-penjualan')->layout('layouts.app', ['title' => $this->title]);
    }
}
