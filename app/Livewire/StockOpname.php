<?php

namespace App\Livewire;

use Livewire\Component;
use App\Utils\TableUtil;

class StockOpname extends Component
{
    public $title;
    public $businessId;

    public function render()
    {
        $this->title = 'Stock Opname';
        $this->businessId = auth()->user()->business_id;

        $query = \App\Models\Product::where('business_id', $this->businessId)->with([
            'category',
            'brand',
            'unit',
            'shelf',
            'productPrices',
        ]);

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('gambar', 'Gambar', false, false),
            TableUtil::setTableHeader('sku', 'SKU', true, true),
            TableUtil::setTableHeader('nama_produk', 'Nama Produk', true, true),
            TableUtil::setTableHeader('product.category.nama_kategori', 'Kategori', true, true),
            TableUtil::setTableHeader('product.brand.nama_brand', 'Merek', true, true),
            TableUtil::setTableHeader('product.shelf.nama_rak', 'Rak', true, true),
            TableUtil::setTableHeader('stok_aktual', 'Stok', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $products = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.stock-opname', [
            'products' => $products,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
