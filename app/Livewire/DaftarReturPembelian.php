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
            'purchasesReturnDetails.stockMovement.batchMovements.productBatch.product',
        ])->where('id', $id)->first();

        DB::beginTransaction();
        try {
            foreach ($purchaseReturn->purchasesReturnDetails as $purchaseReturnDetails) {
                if ($purchaseReturnDetails->stockMovement) {
                    foreach ($purchaseReturnDetails->stockMovement->batchMovements as $batchMovement) {
                        if ($batchMovement->productBatch) {
                            if ($batchMovement->productBatch->product) {
                                $batchMovement->productBatch->product->increment('stok_aktual', $batchMovement->jumlah);
                            }

                            $batchMovement->productBatch->increment('jumlah_saat_ini', $batchMovement->jumlah);
                        }

                        $batchMovement->delete();
                    }

                    $purchaseReturnDetails->stockMovement->delete();
                }

                $purchaseReturnDetails->delete();
            }

            foreach ($purchaseReturn->payments as $payment) {
                $payment->delete();
            }

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
