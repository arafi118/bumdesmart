<?php

namespace App\Livewire;

use App\Traits\WithTable;
use App\Utils\TableUtil;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class DaftarReturPenjualan extends Component
{
    use WithTable;

    public $title;

    public $businessId;

    public $detailSale;

    public $detailRetur;

    public function detailPenjualan($id)
    {
        $sale = \App\Models\Sale::with([
            'customer',
            'business',
            'saleDetails.product',
        ])->where('id', $id)->first();

        $this->detailSale = $sale;

        $this->dispatch('show-modal', modalId: 'detailPenjualanModal');
    }

    public function detailReturPenjualan($id)
    {
        $retur = \App\Models\SalesReturn::with([
            'sale',
            'business',
            'salesReturnDetails.product',
        ])->where('id', $id)->first();

        $this->detailRetur = $retur;

        $this->dispatch('show-modal', modalId: 'detailReturModal');
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $salesReturn = \App\Models\SalesReturn::with([
            'payments',
            'salesReturnDetails',
            'stockMovement.batchMovements.productBatch',
        ])->where('id', $id)->first();

        DB::beginTransaction();
        try {
            $productUpdates = [];
            $batchUpdates = [];
            $batchMovementIds = [];
            $stockMovementIds = [];

            // Undo Stock Movements (Sales Return ADDED stock, so we REMOVE it)
            foreach ($salesReturn->stockMovement as $stockMovement) {
                foreach ($stockMovement->batchMovements as $batchMovement) {
                    if ($batchMovement->productBatch) {
                        $batchId = $batchMovement->batch_id;
                        // For Sales Return: Batch incremented. Undo = Decrement.
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
                    ->decrement('stok_aktual', $jumlah);
            }

            foreach ($batchUpdates as $batchId => $jumlah) {
                \App\Models\ProductBatch::where('id', $batchId)
                    ->decrement('jumlah_saat_ini', $jumlah);
            }

            if (! empty($batchMovementIds)) {
                \App\Models\BatchMovement::whereIn('id', $batchMovementIds)->delete();
            }

            if (! empty($stockMovementIds)) {
                \App\Models\StockMovement::whereIn('id', $stockMovementIds)->delete();
            }

            $salesReturn->salesReturnDetails()->delete();
            $salesReturn->payments()->delete(); // Refund payment (Money Out) should be deleted
            $salesReturn->delete();

            DB::commit();
            $this->dispatch('alert', type: 'success', message: 'Retur berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    #[Layout('layouts.app')]
    #[Title('Daftar Retur Penjualan')]
    public function render()
    {
        $this->businessId = auth()->user()->business_id;

        $query = \App\Models\SalesReturn::where('business_id', $this->businessId);
        if (request()->get('sale_id')) {
            $query->where('sale_id', request()->get('sale_id'));
        }

        $query->with([
            'sale',
        ]);

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('tanggal_return', 'Tanggal Retur', true, true),
            TableUtil::setTableHeader('no_return', 'No. Retur', true, true),
            TableUtil::setTableHeader('sale.no_invoice', 'No. Penjualan', true, true),
            TableUtil::setTableHeader('status', 'Status', true, true),
            TableUtil::setTableHeader('total_return', 'Total Retur', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $salesReturn = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.daftar-retur-penjualan', [
            'salesReturn' => $salesReturn,
            'headers' => $headers,
        ]);
    }
}
