<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Utils\TableUtil;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\StockAdjustmentDetail;
use App\Models\StockAdjustment as StockAdjustmentModel;
use Illuminate\Support\Facades\DB;

class StockAdjustment extends Component
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
    public $jumlah_perubahan;
    public $catatan;

    public $stockMovements = [];
    public $businessId;

    public function render()
    {
        $this->title = 'Stock Adjustment';
        $this->businessId = auth()->user()->business_id;

        $query = StockMovement::where('business_id', $this->businessId)
            ->where('reference_type', 'stok_adjustment')
            ->with('product');

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('product.nama_produk', 'Produk', true, true),
            TableUtil::setTableHeader('tanggal_perubahan_stok', 'Tanggal', true, true),
            TableUtil::setTableHeader('jenis_perubahan', 'Jenis', true, true),
            TableUtil::setTableHeader('jumlah_perubahan', 'Jumlah', true, true),
            TableUtil::setTableHeader('catatan', 'Catatan', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $stocks = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.stock-adjustment', [
            'stock'   => $stocks,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }

    public function edit($id)
    {
        $movement = StockMovement::findOrFail($id);

        $detail = StockAdjustmentDetail::where('stock_adjustment_id', $movement->reference_id)
            ->where('product_id', $movement->product_id)
            ->firstOrFail();

        $product = Product::findOrFail($movement->product_id);

        $this->editId = $movement->id;
        $this->tanggal_perubahan_stok = \Carbon\Carbon::parse($movement->tanggal_perubahan_stok)->format('Y-m-d');

        $this->stok_sistem = $product->stok_aktual - $movement->jumlah_perubahan;
        $this->stok_fisik  = $product->stok_aktual;
        $this->selisih     = $movement->jumlah_perubahan;

        $this->jumlah_perubahan = abs($movement->jumlah_perubahan);
        $this->alasan  = $detail->alasan;
        $this->catatan = $movement->catatan;

        $this->dispatch('show-modal', modalId: 'editStockModal');
    }

    public function updatedStokFisik($value)
    {
        $this->selisih = (int) $value - (int) $this->stok_sistem;
        $this->jumlah_perubahan = abs($this->selisih);
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
            $detail   = StockAdjustmentDetail::where('stock_adjustment_id', $movement->reference_id)
                ->where('product_id', $movement->product_id)
                ->firstOrFail();

            $product = Product::lockForUpdate()->findOrFail($movement->product_id);

            $stokAwal = $product->stok_aktual - $movement->jumlah_perubahan;
            $selisihBaru = $this->stok_fisik - $stokAwal;

            $detail->update([
                'jumlah'      => abs($selisihBaru),
                'jenis'       => $selisihBaru > 0 ? 'in' : 'out',
                'total_harga' => abs($selisihBaru) * $detail->harga_satuan,
                'alasan'      => $this->alasan ?? $detail->alasan,
                'catatan'     => $this->catatan ?? $detail->catatan,
            ]);

            $movement->update([
                'jumlah_perubahan' => $selisihBaru,
                'catatan'          => $this->catatan ?? $movement->catatan,
            ]);

            $product->update([
                'stok_aktual' => $this->stok_fisik
            ]);

            $adjustment = StockAdjustmentModel::find($movement->reference_id);
            if ($adjustment) {
                $adjustment->update([
                    'catatan' => $this->catatan ?? $adjustment->catatan,
                ]);
            }

            DB::commit();

            $this->dispatch('hide-modal', modalId: 'editStockModal');
            $this->dispatch('alert', type: 'success', message: 'Stock adjustment berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        StockMovement::findOrFail($id)->delete();
        $this->dispatch('alert', type: 'success', message: 'Data berhasil dihapus');
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
