<?php

namespace App\Livewire;

use DB;
use Livewire\Attributes\On;
use Livewire\Component;

class TambahReturPembelian extends Component
{
    public $title;

    public $businessId;

    public $purchase = [];

    #[On('save-all')]
    public function saveAll($data)
    {
        if (empty($data['retur_pembelian'])) {
            $this->dispatch('error', 'Tidak ada produk yang dipilih');

            return;
        }

        DB::beginTransaction();
        try {
            $purchaseRetur = \App\Models\PurchasesReturn::create([
                'business_id' => $this->businessId,
                'purchase_id' => $data['purchase_id'],
                'user_id' => auth()->user()->id,
                'no_return' => 'RETUR-'.time(),
                'tanggal_return' => now(),
                'total_return' => $data['total_retur'],
                'alasan_return' => $data['alasan_retur'],
                'status' => 'approved',
            ]);

            foreach ($data['retur_pembelian'] as $retur) {
                $purchaseReturnDetail = \App\Models\PurchasesReturnDetail::create([
                    'purchases_return_id' => $purchaseRetur->id,
                    'purchase_detail_id' => $retur['purchase_detail_id'],
                    'product_id' => $retur['product_id'],
                    'jumlah' => $retur['jumlah'],
                    'harga_satuan' => $retur['harga_satuan'],
                    'sub_total' => $retur['subtotal_retur'],
                ]);

                $stockMovement = \App\Models\StockMovement::create([
                    'business_id' => $this->businessId,
                    'product_id' => $retur['product_id'],
                    'tanggal_perubahan_stok' => now(),
                    'jenis_perubahan' => 'purchase_retur',
                    'jumlah_perubahan' => $retur['jumlah'] * -1,
                    'reference_id' => $purchaseRetur->id,
                    'reference_type' => 'purchases_return',
                    'catatan' => 'Retur pembelian',
                ]);

                $batchMovement = \App\Models\BatchMovement::create([
                    'business_id' => $this->businessId,
                    'batch_id' => $retur['product_batch_id'],
                    'stock_movement_id' => $stockMovement->id,
                    'tanggal_perubahan' => now(),
                    'jenis_transaksi' => 'purchase_retur',
                    'transaction_detail_id' => $purchaseReturnDetail->id,
                    'jumlah' => $retur['jumlah'],
                    'harga_satuan' => $retur['harga_satuan'],
                ]);

                \App\Models\ProductBatch::where('id', $retur['product_batch_id'])->decrement('jumlah_saat_ini', $retur['jumlah']);
                \App\Models\Product::where('id', $retur['product_id'])->decrement('stok_aktual', $retur['jumlah']);
            }

            $payment = \App\Models\Payment::create([
                'business_id' => $this->businessId,
                'user_id' => auth()->user()->id,
                'no_pembayaran' => $purchaseRetur->no_return,
                'tanggal_pembayaran' => $purchaseRetur->tanggal_return,
                'jenis_transaksi' => 'purchase_return',
                'transaction_id' => $purchaseRetur->id,
                'total_harga' => $retur['jumlah'] * $retur['harga_satuan'],
                'metode_pembayaran' => 'tunai',
                'no_referensi' => '-',
                'catatan' => 'Pembayaran Retur Pembelian '.$purchaseRetur->no_return,
                'rekening_debit' => '5.1.01.03',
                'rekening_kredit' => '1.1.03.01',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->dispatch('alert', type: 'success', message: 'Retur pembelian berhasil disimpan');
            $this->dispatch('reset-form');
            $this->dispatch('redirect', url: '/pembelian/daftar-retur', timeout: 1000);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function mount($id = null)
    {
        $this->title = 'Tambah Retur Pembelian';
        $this->businessId = auth()->user()->business_id;

        $this->purchase = \App\Models\Purchase::with([
            'supplier',
            'purchaseDetails.product',
            'purchaseDetails.productBatch',
            'purchaseDetails.purchasesReturnDetail',
        ])->find($id);
    }

    public function render()
    {
        return view('livewire.tambah-retur-pembelian', [
            'purchase' => $this->purchase,
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
