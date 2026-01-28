<?php

namespace App\Livewire;

use App\Traits\WithTable;
use App\Utils\TableUtil;
use DB;
use Livewire\Attributes\On;
use Livewire\Component;

class DaftarReturPembelian extends Component
{
    use WithTable;

    public $title;

    public $businessId;

    public $detailPurchase;

    public $detailRetur;

    public function detailPembelian($id)
    {
        $purchase = \App\Models\Purchase::with([
            'supplier',
            'business',
            'purchaseDetails.product',
        ])->where('id', $id)->first();

        $this->detailPurchase = $purchase;

        $this->dispatch('show-modal', modalId: 'detailPembelianModal');
    }

    public function detailReturPembelian($id)
    {
        $retur = \App\Models\PurchasesReturn::with([
            'purchase',
            'business',
            'purchasesReturnDetails.product',
        ])->where('id', $id)->first();

        $this->detailRetur = $retur;

        $this->dispatch('show-modal', modalId: 'detailReturModal');
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $purchaseReturn = \App\Models\PurchasesReturn::with([
            'payments',
            'purchasesReturnDetails',
            'stockMovement.batchMovements.productBatch',
        ])->where('id', $id)->first();

        DB::beginTransaction();
        try {
            $productUpdates = [];
            $batchUpdates = [];
            $batchMovementIds = [];
            $stockMovementIds = [];

            foreach ($purchaseReturn->stockMovement as $stockMovement) {
                foreach ($stockMovement->batchMovements as $batchMovement) {
                    if ($batchMovement->productBatch) {
                        $batchId = $batchMovement->batch_id;
                        $batchUpdates[$batchId] = ($batchUpdates[$batchId] ?? 0) + $batchMovement->jumlah;

                        $productId = $batchMovement->productBatch->product_id;
                        $productUpdates[$productId] = ($productUpdates[$productId] ?? 0) + $batchMovement->jumlah;

                        $batchMovementIds[] = $batchMovement->id;
                    }

                    $stockMovementIds[] = $stockMovement->id;
                }
            }

            foreach ($productUpdates as $productId => $jumlah) {
                \App\Models\Product::where('id', $productId)
                    ->increment('stok_aktual', $jumlah);
            }

            foreach ($batchUpdates as $batchId => $jumlah) {
                \App\Models\ProductBatch::where('id', $batchId)
                    ->increment('jumlah_saat_ini', $jumlah);
            }

            if (! empty($batchMovementIds)) {
                \App\Models\BatchMovement::whereIn('id', $batchMovementIds)->delete();
            }

            if (! empty($stockMovementIds)) {
                \App\Models\StockMovement::whereIn('id', $stockMovementIds)->delete();
            }

            $purchaseReturn->purchasesReturnDetails()->delete();
            $purchaseReturn->payments()->delete();
            $purchaseReturn->delete();

            DB::commit();
            $this->dispatch('alert', type: 'success', message: 'Retur berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        $this->title = 'Daftar Retur Pembelian';
        $this->businessId = auth()->user()->business_id;

        $query = \App\Models\PurchasesReturn::where('business_id', $this->businessId);
        if (request()->get('purchase_id')) {
            $query->where('purchase_id', request()->get('purchase_id'));
        }

        $query->with([
            'purchase',
        ]);

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('tanggal_return', 'Tanggal Retur', true, true),
            TableUtil::setTableHeader('no_return', 'No. Retur', true, true),
            TableUtil::setTableHeader('purchase.no_pembelian', 'No. Pembelian', true, true),
            TableUtil::setTableHeader('status', 'Status', true, true),
            TableUtil::setTableHeader('total_return', 'Total Retur', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $purchasesReturn = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.daftar-retur-pembelian', [
            'purchasesReturn' => $purchasesReturn,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
