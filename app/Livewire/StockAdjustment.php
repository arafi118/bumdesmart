<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockAdjustment as StockAdjustmentModel;
use App\Models\StockMovement;
use App\Utils\TableUtil;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class StockAdjustment extends Component
{
    use \Livewire\WithPagination;

    public $title;

    public $search = '';

    public $businessId;

    // Properties for modal
    public $adjustmentDetail; // Renamed from stockdetail to reflect new model

    public $stockdetail; // Keeping for backward compatibility if view needs it, but initialized as null

    public $product; // Keeping for backward compatibility

    public $titleModal;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->title = 'Daftar Stock Adjustment';
        $this->businessId = auth()->user()->business_id;

        $query = StockAdjustmentModel::where('business_id', $this->businessId)
            ->where(function ($q) {
                $q->where('no_penyesuaian', 'like', '%'.$this->search.'%')
                    ->orWhere('catatan', 'like', '%'.$this->search.'%')
                    ->orWhere('status', 'like', '%'.$this->search.'%');
            })
            ->with(['user'])
            ->orderBy('created_at', 'desc');

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('no_penyesuaian', 'No. Ref', true, true),
            TableUtil::setTableHeader('tanggal_penyesuaian', 'Tanggal', true, true),
            TableUtil::setTableHeader('jenis_penyesuaian', 'Jenis', true, true),
            TableUtil::setTableHeader('status', 'Status', true, true),
            TableUtil::setTableHeader('user.nama_lengkap', 'Oleh', true, true),
            TableUtil::setTableHeader('catatan', 'Catatan', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $adjustments = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.stock-adjustment', [
            'adjustments' => $adjustments,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }

    #[On('approve-confirmed')]
    public function approve($id)
    {
        $adjustment = StockAdjustmentModel::with('details')->findOrFail($id);

        if ($adjustment->status !== 'draft') {
            $this->dispatch('alert', type: 'error', message: 'Hanya status draft yang dapat disetujui');

            return;
        }

        DB::beginTransaction();

        try {
            // Update Status
            $adjustment->update([
                'status' => 'approved',
                // 'approved_by' => auth()->id(), // If we add approved_by column later
            ]);

            // Execute Stock Movements
            foreach ($adjustment->details as $detail) {
                // Update Product Stock
                $product = Product::lockForUpdate()->find($detail->product_id);
                if ($product) {
                    $product->stok_aktual += $detail->jumlah * ($detail->jenis === 'in' ? 1 : -1);
                    $product->save();
                }

                // Create Movement
                StockMovement::create([
                    'business_id' => $this->businessId,
                    'product_id' => $detail->product_id,
                    'tanggal_perubahan_stok' => $adjustment->tanggal_penyesuaian,
                    'jenis_perubahan' => 'stock_adjustment',
                    'jumlah_perubahan' => $detail->jumlah * ($detail->jenis === 'in' ? 1 : -1),
                    'reference_id' => $adjustment->id,
                    'reference_type' => 'stock_adjustment',
                    'catatan' => $detail->alasan ?? $adjustment->catatan ?? 'Stock Adjustment Approved',
                ]);
            }

            DB::commit();
            $this->dispatch('alert', type: 'success', message: 'Stock Adjustment berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: 'Gagal menyetujui: '.$e->getMessage());
        }
    }

    public function detailStock($id)
    {
        $this->adjustmentDetail = StockAdjustmentModel::with([
            'details.product.unit',
            'user',
        ])->findOrFail($id);

        $this->titleModal = 'Detail Penyesuaian Stok: '.$this->adjustmentDetail->no_penyesuaian;

        $this->dispatch('show-modal', modalId: 'detailProdukModal');
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $adjustment = StockAdjustmentModel::findOrFail($id);

        if ($adjustment->status !== 'draft') {
            $this->dispatch('alert', type: 'error', message: 'Hanya adjustment status draft yang dapat dihapus');

            return;
        }

        DB::transaction(function () use ($adjustment) {
            $adjustment->details()->delete();
            $adjustment->delete();
        });

        $this->dispatch('alert', type: 'success', message: 'Adjustment berhasil dihapus');
    }
}
