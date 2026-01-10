<?php

namespace App\Livewire;

use App\Traits\WithTable;
use App\Utils\TableUtil;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Storage;

class Produk extends Component
{
    use WithFileUploads, WithTable;

    public $title;

    public $titleModal;

    public $id;

    public $businessId;

    public $kategori;

    public $merek;

    public $satuan;

    public $rakPenyimpanan;

    public $sku;

    public $namaProduk;

    public $hargaBeliDefault;

    public $hargaJualDefault;

    public $stokMinimal;

    public $gambar;

    public $displayGambar = 'products/no-image.png';

    public $activeTab = 'daftarProduk';

    public $aktif = 1;

    public $product;

    protected function rules()
    {
        return [
            'sku' => [
                'required',
                Rule::unique('products', 'sku')->ignore($this->id),
            ],
            'namaProduk' => 'required',
            'kategori' => 'required',
            'merek' => 'required',
            'satuan' => 'required',
            'rakPenyimpanan' => 'nullable',
            'hargaBeliDefault' => 'required',
            'hargaJualDefault' => 'required',
            'stokMinimal' => 'required',
            'gambar' => [
                'nullable',
                Rule::imageFile()->max(5120),
            ],
            'aktif' => 'required',
        ];
    }

    public function resetForm()
    {
        $this->reset('id', 'sku', 'namaProduk', 'kategori', 'merek', 'satuan', 'rakPenyimpanan', 'hargaBeliDefault', 'hargaJualDefault', 'stokMinimal', 'gambar', 'aktif');
    }

    public function create()
    {
        $this->resetForm();
        $this->title = 'Tambah Produk';
        $this->activeTab = 'formProduk';

        $this->dispatch('switch-tab', tabId: 'formProduk');
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->resetValidation();
        $this->titleModal = 'Ubah Rak Penyimpanan';

        $product = \App\Models\Product::find($id);

        $this->sku = $product->sku;
        $this->namaProduk = $product->nama_produk;
        $this->kategori = $product->category_id;
        $this->merek = $product->brand_id;
        $this->satuan = $product->unit_id;
        $this->rakPenyimpanan = $product->shelf_id;
        $this->hargaBeliDefault = number_format($product->harga_beli);
        $this->hargaJualDefault = number_format($product->harga_jual);
        $this->stokMinimal = $product->stok_minimal;
        $this->aktif = $product->is_active;
        $this->id = $product->id;

        $this->displayGambar = $product->gambar;
        $this->activeTab = 'formProduk';

        $this->dispatch('switch-tab', tabId: 'formProduk');
    }

    public function back()
    {
        $this->activeTab = 'daftarProduk';
        $this->dispatch('switch-tab', tabId: 'daftarProduk');
    }

    public function store()
    {
        $this->validate();

        $data = [
            'business_id' => $this->businessId,
            'category_id' => $this->kategori,
            'brand_id' => $this->merek,
            'unit_id' => $this->satuan,
            'shelf_id' => $this->rakPenyimpanan,
            'sku' => $this->sku,
            'nama_produk' => $this->namaProduk,
            'harga_beli' => floatval(str_replace(',', '', $this->hargaBeliDefault)),
            'harga_jual' => floatval(str_replace(',', '', $this->hargaJualDefault)),
            'stok_minimal' => $this->stokMinimal,
            'stok_aktual' => 0,
            'metode_biaya' => 'FIFO',
            'biaya_rata_rata' => 0,
            'gambar' => 'products/no-image.png',
            'aktif' => $this->aktif,
        ];

        if ($this->id) {
            $produkLama = \App\Models\Product::find($this->id);

            $data['gambar'] = $produkLama->gambar;
            if ($this->gambar) {
                $data['gambar'] = $this->gambar->storeAs('products', time().'.'.$this->gambar->getClientOriginalExtension());
                if ($produkLama->gambar != 'products/no-image.png') {
                    Storage::delete($produkLama->gambar);
                }
            }

            $data['stok_aktual'] = $produkLama->stok_aktual;
            $data['biaya_rata_rata'] = $produkLama->biaya_rata_rata;

            \App\Models\Product::find($this->id)->update($data);
            $message = 'Produk berhasil diubah';
        } else {
            if ($this->gambar) {
                $data['gambar'] = $this->gambar->storeAs('products', time().'.'.$this->gambar->getClientOriginalExtension());
            }

            \App\Models\Product::create($data);
            $message = 'Produk berhasil ditambahkan';
        }

        $this->activeTab = 'daftarProduk';
        $this->dispatch('switch-tab', tabId: 'daftarProduk');
        $this->dispatch('alert', type: 'success', message: $message);
        $this->resetForm();
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $product = \App\Models\Product::find($id);
        if ($product->gambar != 'products/no-image.png') {
            Storage::delete($product->gambar);
        }
        $product->delete();

        $this->dispatch('alert', type: 'success', message: 'Produk berhasil dihapus');
    }

    public function detailProduk($id)
    {
        $this->product = \App\Models\Product::find($id)->with([
            'category',
            'brand',
            'unit',
            'shelf',
        ])->first();

        $this->titleModal = $this->product->nama_produk;

        $this->dispatch('show-modal', modalId: 'detailProdukModal');
    }

    public function render()
    {
        $this->title = 'Produk';
        $this->businessId = auth()->user()->business_id;

        $query = \App\Models\Product::where('business_id', $this->businessId)->with([
            'category',
            'brand',
            'unit',
            'shelf',
        ]);

        $categories = \App\Models\Category::where('business_id', $this->businessId)->get();
        $brands = \App\Models\Brand::where('business_id', $this->businessId)->get();
        $units = \App\Models\Unit::where('business_id', $this->businessId)->get();
        $shelves = \App\Models\Shelves::where('business_id', $this->businessId)->get();

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

        return view('livewire.produk', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'units' => $units,
            'shelves' => $shelves,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
