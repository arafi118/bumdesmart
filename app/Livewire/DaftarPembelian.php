<?php

namespace App\Livewire;

use App\Traits\WithTable;
use App\Utils\TableUtil;
use Livewire\Component;

class DaftarPembelian extends Component
{
    use WithTable;

    public $title;

    public $businessId;

    public $detailPurchase = [];

    public function detailPembelian($id)
    {
        $purchase = \App\Models\Purchase::with([
            'supplier',
            'business',
            'purchaseDetails.product',
            'payments' => function ($query) {
                $query->where(function ($query) {
                    $query->where('rekening_debit', '1.1.03.01')->where('rekening_kredit', 'like', '1.1.01%');
                })->orWhere(function ($query) {
                    $query->where('rekening_debit', '2.1.01.01')->where('rekening_kredit', 'like', '1.1.01%');
                });
            },
        ])->where('id', $id)->first();

        $this->detailPurchase = $purchase;

        $this->dispatch('show-modal', modalId: 'detailPembelianModal');
    }

    public function render()
    {
        $this->title = 'Daftar Pembelian';
        $this->businessId = auth()->user()->business_id;

        $query = \App\Models\Purchase::where('business_id', $this->businessId)->with([
            'supplier',
            'payments' => function ($query) {
                $query->where(function ($query) {
                    $query->where('rekening_debit', '1.1.03.01')->where('rekening_kredit', 'like', '1.1.01%');
                })->orWhere(function ($query) {
                    $query->where('rekening_debit', '2.1.01.01')->where('rekening_kredit', 'like', '1.1.01%');
                });
            },
        ]);

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('no_pembelian', 'No. Pembelian', true, true),
            TableUtil::setTableHeader('tanggal_pembelian', 'Tanggal Pembelian', true, true),
            TableUtil::setTableHeader('supplier.nama_supplier', 'Supplier', true, true),
            TableUtil::setTableHeader('status', 'Status', true, true),
            TableUtil::setTableHeader('total', 'Total Pembelian', false, false),
            TableUtil::setTableHeader('id', 'Total Pembayaran', false, false),
            TableUtil::setTableHeader('id', 'Sisa Pembayaran', false, false),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $purchases = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.daftar-pembelian', [
            'purchases' => $purchases,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
