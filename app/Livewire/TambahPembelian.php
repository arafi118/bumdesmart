<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Supplier;
use App\Traits\WithTable;
use Livewire\Component;

class TambahPembelian extends Component
{
    use WithTable;

    public $title;

    public $titleModal;

    public $modalId;

    public $businessId;

    public $nomorPembelian;

    public $tanggalPembelian;

    public $supplier;

    public $products = [];

    public $product = [
        'id' => '',
        'sku' => '',
        'nama_produk' => '',
        'harga_beli' => '',
        'jumlah_beli' => '',
        'diskon' => [
            'jenis' => 'nominal',
            'jumlah' => '',
            'nominal' => '',
        ],
        'cashback' => [
            'jenis' => 'nominal',
            'jumlah' => '',
            'nominal' => '',
        ],
        'subtotal' => '',
    ];

    public $totalProducts = [
        'harga_beli' => 0,
        'jumlah_beli' => 0,
        'diskon' => 0,
        'cashback' => 0,
        'subtotal' => 0,
    ];

    public $diskon = [
        'jenis' => 'nominal',
        'jumlah' => '',
        'nominal' => '',
    ];

    public $cashback = [
        'jenis' => 'nominal',
        'jumlah' => '',
        'nominal' => '',
    ];

    public $jenisPajak;

    public $total;

    public $catatan;

    public $jenisPembayaran = 'cash';

    public $noRekening;

    public $searchTerm = '';

    public $searchProduct = '';

    public function loadSuppliers($query, $offset = 0)
    {
        $perPage = 50;

        $suppliers = Supplier::select(
            'id',
            'nama_supplier',
        )->where('nama_supplier', 'LIKE', "%{$query}%")
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $total = Supplier::where('nama_supplier', 'LIKE', "%{$query}%")->count();
        $hasMore = ($offset + $perPage) < $total;

        return [
            'data' => $suppliers,
            'after' => $hasMore ? ($offset + $perPage) : null,
        ];
    }

    public function loadSearchProducts($query, $offset = 0)
    {
        $perPage = 20;

        $productsQuery = Product::where('nama_produk', 'LIKE', "%{$query}%")->orWhere('sku', 'LIKE', "%{$query}%")
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $total = Product::where('nama_produk', 'LIKE', "%{$query}%")->orWhere('sku', 'LIKE', "%{$query}%")->count();
        $hasMore = ($offset + $perPage) < $total;

        $products = [];
        foreach ($productsQuery as $product) {
            $products[] = [
                'id' => $product->id,
                'nama_produk' => $product->nama_produk,
                'product' => $product,
            ];
        }

        return [
            'data' => $products,
            'after' => $hasMore ? ($offset + $perPage) : null,
        ];
    }

    public function resetNoRekening()
    {
        if ($this->jenisPembayaran == 'cash') {
            $this->noRekening = '';
        }
    }

    public function addProduct($product)
    {
        if (isset($this->products[$product['id']])) {
            $newProduct = $this->products[$product['id']];
            $newProduct['harga_beli'] = str_replace(',', '', $newProduct['harga_beli']);

            $newProduct['jumlah_beli']++;
            $newProduct['subtotal'] = number_format($newProduct['harga_beli'] * $newProduct['jumlah_beli']);
            $newProduct['harga_beli'] = number_format($newProduct['harga_beli']);
            $this->products[$product['id']] = $newProduct;
        } else {
            $addProduct = [
                'id' => $product['id'],
                'sku' => $product['sku'],
                'nama_produk' => $product['nama_produk'],
                'harga_beli' => number_format($product['harga_beli']),
                'jumlah_beli' => 1,
                'diskon' => [
                    'jenis' => 'nominal',
                    'jumlah' => 0,
                    'nominal' => 0,
                ],
                'cashback' => [
                    'jenis' => 'nominal',
                    'jumlah' => 0,
                    'nominal' => 0,
                ],
                'subtotal' => number_format($product['harga_beli']),
            ];

            $this->products[$product['id']] = $addProduct;
        }

        $this->setTotal();
    }

    public function setDiscountProduct()
    {
        $product = $this->product;
        $hargaBeli = str_replace(',', '', $product['harga_beli']);
        $jumlahDiskon = str_replace(',', '', $product['diskon']['jumlah']);

        $diskon = 0;
        if ($product['diskon']['jenis'] == 'nominal') {
            $diskon = $jumlahDiskon;
        } else {
            $diskon = ($jumlahDiskon > 0) ? ($hargaBeli * $product['jumlah_beli'] * $jumlahDiskon / 100) : 0;
        }

        $product['diskon']['nominal'] = $diskon;
        $product['diskon']['nominal'] = number_format($diskon);
        $product['subtotal'] = number_format($hargaBeli * $product['jumlah_beli'] - $diskon);

        $this->dispatch('hide-modal', modalId: $this->modalId);
        $this->products[$product['id']] = $product;
        $this->product = [];

        $this->setTotal();
    }

    public function setCashbackProduct()
    {
        $product = $this->product;
        $hargaBeli = str_replace(',', '', $product['harga_beli']);
        $diskon = str_replace(',', '', $product['diskon']['nominal']);
        $jumlahCashback = str_replace(',', '', $product['cashback']['jumlah']);

        $cashback = 0;
        if ($product['cashback']['jenis'] == 'nominal') {
            $cashback = $jumlahCashback;
        } else {
            $cashback = ($jumlahCashback > 0) ? ($hargaBeli * $product['jumlah_beli'] * $jumlahCashback / 100) : 0;
        }

        $product['cashback']['nominal'] = number_format($cashback);
        $product['subtotal'] = number_format($hargaBeli * $product['jumlah_beli'] - $diskon + $cashback);

        $this->dispatch('hide-modal', modalId: $this->modalId);
        $this->products[$product['id']] = $product;
        $this->product = [];

        $this->setTotal();
    }

    public function updatedProducts($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            $productId = $parts[0];
            $field = $parts[1];

            if (in_array($field, ['harga_beli', 'jumlah_beli'])) {
                $product = $this->products[$productId];
                $hargaBeli = str_replace(',', '', $product['harga_beli']);
                $jumlahBeli = $product['jumlah_beli'];
                $diskon = str_replace(',', '', $product['diskon']['nominal']);
                $cashback = str_replace(',', '', $product['cashback']['nominal']);

                $this->products[$productId]['subtotal'] = number_format($hargaBeli * $jumlahBeli - $diskon + $cashback);
                $this->calculateTotal();
            }
        }
    }

    public function calculateTotal()
    {
        $totalHargaBeli = 0;
        $totalJumlahBeli = 0;
        $totalDiskon = 0;
        $totalCashback = 0;
        $totalSubtotal = 0;

        foreach ($this->products as $product) {
            $hargaBeli = str_replace(',', '', $product['harga_beli']);
            $jumlahBeli = $product['jumlah_beli'];
            $diskon = str_replace(',', '', $product['diskon']['nominal']);
            $cashback = str_replace(',', '', $product['cashback']['nominal']);
            $subtotal = $hargaBeli * $jumlahBeli - $diskon + $cashback;

            $totalHargaBeli += $hargaBeli;
            $totalJumlahBeli += $jumlahBeli;
            $totalDiskon += $diskon;
            $totalCashback += $cashback;
            $totalSubtotal += $subtotal;
        }

        $this->totalProducts['harga_beli'] = $totalHargaBeli;
        $this->totalProducts['jumlah_beli'] = $totalJumlahBeli;
        $this->totalProducts['diskon'] = $totalDiskon;
        $this->totalProducts['cashback'] = $totalCashback;
        $this->totalProducts['subtotal'] = $totalSubtotal;
    }

    public function setTotal()
    {
        $this->calculateTotal();
    }

    public function openModal($id, $modalId)
    {
        $product = $this->products[$id];
        $this->product = $product;

        $this->modalId = $modalId;
        $this->dispatch('show-modal', modalId: $modalId);
    }

    public function removeProduct($id)
    {
        unset($this->products[$id]);
        $this->setTotal();
    }

    public function render()
    {
        $this->title = 'Tambah Pembelian';
        $this->businessId = auth()->user()->business_id;

        $this->tanggalPembelian = date('Y-m-d');

        return view('livewire.tambah-pembelian')->layout('layouts.app', ['title' => $this->title]);
    }
}
