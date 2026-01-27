<?php

namespace App\Livewire;

use Livewire\Component;
use App\Utils\TableUtil;

use function Laravel\Prompts\table;

class StockOpname extends Component
{
    public $title;
    public $businessId;

    public function render()
    {
        $this->title = 'Stock Opname';
        $this->businessId = auth()->user()->business_id;

        $query = \App\Models\StockMovement::where('business_id', $this->businessId)->with([
            'product',
        ]);

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('product.nama_produk', 'Product', true, true),
            TableUtil::setTableHeader('tanggal_perubahan_stok', 'Tanggal Perubahan Stok', true, true),
            TableUtil::setTableHeader('jenis_perubahan', 'Jenis Perubahan', true, true),
            TableUtil::setTableHeader('jumlah_perubahan', 'Jumlah Perubahan', true, true),
            TableUtil::setTableHeader('reference_id', 'Reference ID', true, true),
            TableUtil::setTableHeader('reference_type', 'Reference Type', true, true),
            TableUtil::setTableHeader('catatan', 'Catatan', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $stock = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.stock-opname', [
            'stock' => $stock,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
