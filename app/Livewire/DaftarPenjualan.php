<?php

namespace App\Livewire;

use App\Traits\WithTable;
use App\Utils\TableUtil;
use Livewire\Component;

class DaftarPenjualan extends Component
{
    use WithTable;

    public $title;

    public $businessId;

    public $detailSale;

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

    public function render()
    {
        $this->title = 'Daftar Penjualan';
        $this->businessId = auth()->user()->business_id;

        $query = \App\Models\Sale::where('business_id', $this->businessId)->with([
            'customer',
            'saleReturn',
            'payments',
        ]);

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('no_invoice', 'No. Invoice', true, true),
            TableUtil::setTableHeader('tanggal_transaksi', 'Tanggal Transaksi', true, true),
            TableUtil::setTableHeader('customer.nama_pelanggan', 'Pelanggan', true, true),
            TableUtil::setTableHeader('status', 'Status', true, true),
            TableUtil::setTableHeader('total', 'Total Harga', false, false),
            TableUtil::setTableHeader('id', 'Total Pembayaran', false, false),
            TableUtil::setTableHeader('id', 'Sisa Pembayaran', false, false),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $sales = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.daftar-penjualan', [
            'sales' => $sales,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
