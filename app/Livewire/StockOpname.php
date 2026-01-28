<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Utils\TableUtil;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\StockOpnameDetail;
use App\Models\StockOpname as StockOpnameModel;
use Illuminate\Support\Facades\DB;

class StockOpname extends Component
{
    public $title;
    public $titleModal;
    public $product;
    public $stockdetail;
    public $stok_sistem;
    public $stok_fisik;
    public $selisih;
    public $alasan;
    public $editId;
    public $tanggal_perubahan_stok;
    public $jenis_perubahan;
    public $jumlah_perubahan;
    public $catatan;

    public $stockMovements = [];
    public $businessId;

    public function render()
    {
        $this->title = 'Stock Opname';
        $this->businessId = auth()->user()->business_id;

        $query = StockMovement::where('business_id', $this->businessId)
            ->where('reference_type', 'stock_opname')
            ->with('product');

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('product.nama_produk', 'Product', true, true),
            TableUtil::setTableHeader('tanggal_perubahan_stok', 'Tanggal Perubahan Stok', true, true),
            TableUtil::setTableHeader('jenis_perubahan', 'Jenis Perubahan', true, true),
            TableUtil::setTableHeader('jumlah_perubahan', 'Jumlah Perubahan', true, true),
            TableUtil::setTableHeader('catatan', 'Catatan', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $stocks = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.stock-opname', [
            'stock' => $stocks,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }

    public function edit($id)
    {
        $movement = StockMovement::findOrFail($id);

        $detail = StockOpnameDetail::where('stock_opname_id', $movement->reference_id)
            ->where('product_id', $movement->product_id)
            ->firstOrFail();

        $this->editId = $movement->id;
        $this->tanggal_perubahan_stok = \Carbon\Carbon::parse($movement->tanggal_perubahan_stok)->format('Y-m-d');
        $this->jumlah_perubahan = $movement->jumlah_perubahan;
        $this->catatan = $movement->catatan;
        $this->stok_sistem = $detail->stok_sistem;
        $this->stok_fisik = $detail->stok_fisik;
        $this->selisih = $detail->selisih;
        $this->alasan = $detail->alasan;

        $this->dispatch('show-modal', modalId: 'editStockModal');
    }

    public function updatedStokFisik($value)
    {
        $this->selisih = (int)$value - (int)$this->stok_sistem;
    }


    public function update()
    {
        $this->validate([
            'stok_fisik' => 'required|numeric|min:0',
            'alasan'     => 'nullable|string',
            'catatan'    => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $movement = StockMovement::findOrFail($this->editId);

            $detail = StockOpnameDetail::where('stock_opname_id', $movement->reference_id)
                ->where('product_id', $movement->product_id)
                ->firstOrFail();

            $stockOpname = StockOpnameModel::find($movement->reference_id);

            $selisih = $this->stok_fisik - $detail->stok_sistem;
            $totalHarga = $selisih * $detail->harga_satuan;

            $detail->update([
                'stok_fisik'  => $this->stok_fisik,
                'selisih'     => $selisih,
                'total_harga' => $totalHarga,
                'alasan'      => $this->alasan ?? $detail->alasan,
                'catatan'     => $this->catatan ?? $detail->catatan,
            ]);

            $movement->update([
                'jumlah_perubahan' => $selisih,
                'catatan'          => $this->catatan ?? $movement->catatan,
            ]);

            if ($stockOpname) {
                $stockOpname->update([
                    'catatan' => $this->catatan ?? $stockOpname->catatan,
                ]);
            }

            $product = Product::find($detail->product_id);
            if ($product) {
                $product->update([
                    'stok_aktual' => $this->stok_fisik
                ]);
            }

            DB::commit();

            $this->dispatch('hide-modal', modalId: 'editStockModal');
            $this->dispatch('alert', type: 'success', message: 'Stock opname berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        StockMovement::findOrFail($id)->delete();
        $this->dispatch('alert', type: 'success', message: 'Produk berhasil dihapus');
    }

    public function detailStock($id)
    {
        $this->stockdetail = StockMovement::with([
            'product.category',
            'product.brand',
            'product.unit',
            'product.shelf',
        ])->findOrFail($id);

        $this->product = $this->stockdetail->product;

        $this->stockMovements = StockMovement::where('product_id', $this->product->id)
            ->where('business_id', $this->businessId)
            ->orderBy('tanggal_perubahan_stok', 'desc')
            ->get();

        $this->titleModal = $this->product->nama_produk;

        $this->dispatch('show-modal', modalId: 'detailProdukModal');
    }
}
