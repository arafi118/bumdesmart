<?php

namespace App\Livewire;

use Livewire\Component;

class TambahReturPembelian extends Component
{

    public $title;

    public $businessId;

    public $purchase = [];

    public function mount($id = null)
    {
        $this->title = 'Tambah Retur Pembelian';
        $this->businessId = auth()->user()->business_id;

        $this->purchase = \App\Models\Purchase::with([
            'supplier',
            'purchaseDetails.product'
        ])->find($id);
    }

    public function render()
    {
        return view('livewire.tambah-retur-pembelian',[
            'purchase' => $this->purchase
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
