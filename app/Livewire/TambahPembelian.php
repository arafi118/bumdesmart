<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Traits\WithTable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class TambahPembelian extends Component
{
    use WithTable;

    public $title;

    public $businessId;

    // Form Fields (Livewire still tracks these for initial binding/validation if needed,
    // but primary interaction is client-side)
    public $nomorPembelian;

    public $tanggalPembelian;

    public $supplier;

    public $catatan;

    // Payment Fields
    public $jenisPembayaran = 'cash';

    public $noRekening;

    // Search Fields (Livewire handles the search queries)
    public $searchTerm = '';

    public $searchProduct = '';

    public function mount()
    {
        $this->title = 'Tambah Pembelian';
        $this->businessId = auth()->user()->business_id;
        $this->tanggalPembelian = date('Y-m-d');
    }

    public function loadSuppliers($query, $offset = 0)
    {
        $perPage = 50;

        $suppliers = Supplier::select('id', 'nama_supplier')
            ->where('business_id', $this->businessId) // Ensure business scope
            ->where('nama_supplier', 'LIKE', "%{$query}%")
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $total = Supplier::where('business_id', $this->businessId)
            ->where('nama_supplier', 'LIKE', "%{$query}%")
            ->count();

        $hasMore = ($offset + $perPage) < $total;

        return [
            'data' => $suppliers,
            'after' => $hasMore ? ($offset + $perPage) : null,
        ];
    }

    public function loadSearchProducts($query, $offset = 0)
    {
        $perPage = 20;

        $productsQuery = Product::where('business_id', $this->businessId)
            ->where(function ($q) use ($query) {
                $q->where('nama_produk', 'LIKE', "%{$query}%")
                    ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $total = Product::where('business_id', $this->businessId)
            ->where(function ($q) use ($query) {
                $q->where('nama_produk', 'LIKE', "%{$query}%")
                    ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->count();

        $hasMore = ($offset + $perPage) < $total;

        $products = [];
        foreach ($productsQuery as $product) {
            $products[] = [
                'id' => $product->id,
                'nama_produk' => $product->nama_produk,
                'sku' => $product->sku,
                'harga_beli' => $product->harga_beli,
                'gambar' => $product->gambar,
                // Pass full object if needed, but array is usually enough
            ];
        }

        return [
            'data' => $products,
            'after' => $hasMore ? ($offset + $perPage) : null,
        ];
    }

    #[On('save-all')]
    public function saveAll($data)
    {
        if (empty($data['products'])) {
            $this->dispatch('error', 'Belum ada produk yang dipilih');

            return;
        }

        $bayar = $this->parseNumber($data['bayar']);
        $total = $this->parseNumber($data['grandTotal']);

        $jenisPembayaran = $data['jenisPembayaran'];
        $status = $data['status'] ?? 'pending';

        // Enforce Server-Side Logic
        if ($bayar < $total) {
            // If underpaid, force credit unless it's a preorder
            if ($jenisPembayaran !== 'preorder') {
                $jenisPembayaran = 'credit';
            }
            $status = 'partial';
        } else {
            // If fully paid, force cash if it was credit
            if ($jenisPembayaran === 'credit') {
                $jenisPembayaran = 'cash';
            }

            if ($jenisPembayaran === 'preorder') {
                $status = 'paid';
            } else {
                $status = 'completed';
            }
        }

        DB::beginTransaction();
        try {
            $subtotal = $this->parseNumber($data['subtotal']);

            $globalDiskonVal = $this->parseNumber($data['globalDiskon']['jumlah']);
            $globalDiskonAmt = ($data['globalDiskon']['jenis'] === 'nominal')
                ? $globalDiskonVal
                : ($subtotal * $globalDiskonVal / 100);

            $taxable = max(0, $subtotal - $globalDiskonAmt);
            $taxAmount = ($data['jenisPajak'] === 'PPN') ? $taxable * 0.11 : 0;

            $keterangan = $data['catatan'] ?? '';
            if (! empty($data['noRekening'])) {
                $keterangan .= ' [Transfer: '.$data['noRekening'].']';
            }

            $purchase = Purchase::create([
                'no_pembelian' => $data['nomorPembelian'] ?? 'PO-'.time(),
                'tanggal_pembelian' => $data['tanggalPembelian'],
                'business_id' => $this->businessId,
                'supplier_id' => $data['supplier'],
                'user_id' => auth()->id(),
                'jenis_pembayaran' => $jenisPembayaran,
                'subtotal' => $subtotal,
                'jenis_diskon' => $data['globalDiskon']['jenis'],
                'jumlah_diskon' => $globalDiskonVal,
                'jenis_cashback' => $data['globalCashback']['jenis'],
                'jumlah_cashback' => $this->parseNumber($data['globalCashback']['jumlah']),
                'jumlah_pajak' => $taxAmount,
                'total' => $total,
                'dibayar' => $bayar,
                'kembalian' => $this->parseNumber($data['kembalian']),
                'jumlah_utang' => max(0, $total - $bayar),
                'status' => $status,
                'keterangan' => $keterangan,
            ]);

            foreach ($data['products'] as $item) {
                $purchase->purchaseDetails()->create([
                    'product_id' => $item['id'],
                    'jumlah' => $item['jumlah_beli'],
                    'harga_satuan' => $this->parseNumber($item['harga_beli']),
                    'jenis_diskon' => $item['diskon']['jenis'] ?? 'nominal',
                    'jumlah_diskon' => $this->parseNumber($item['diskon']['jumlah'] ?? 0),
                    'jenis_cashback' => $item['cashback']['jenis'] ?? 'nominal',
                    'jumlah_cashback' => $this->parseNumber($item['cashback']['jumlah'] ?? 0),
                    'subtotal' => $this->parseNumber($item['subtotal']),
                ]);
            }

            DB::commit();
            $this->dispatch('success', 'Transaksi berhasil disimpan');
            $this->dispatch('reset-cart');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    private function parseNumber($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        return (float) str_replace(',', '', $value);
    }

    public function render()
    {
        return view('livewire.tambah-pembelian')->layout('layouts.app', ['title' => $this->title]);
    }
}
