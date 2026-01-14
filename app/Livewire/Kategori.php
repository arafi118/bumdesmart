<?php

namespace App\Livewire;

use App\Traits\WithTable;
use App\Utils\MaterialIcon;
use App\Utils\TableUtil;
use Livewire\Attributes\On;
use Livewire\Component;

class Kategori extends Component
{
    use WithTable;

    public $title;

    public $titleModal;

    public $id;

    public $businessId;

    public $namaKategori;

    public $deskripsi;

    public $icon;

    protected function rules()
    {
        return [
            'namaKategori' => 'required',
            'deskripsi' => 'nullable',
            'icon' => 'nullable',
        ];
    }

    public function resetForm()
    {
        $this->reset('namaKategori', 'deskripsi', 'icon', 'id');
    }

    public function create()
    {
        $this->resetForm();
        $this->titleModal = 'Tambah Kategori';

        $this->dispatch('show-modal', modalId: 'kategoriModal');
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->resetValidation();
        $this->titleModal = 'Ubah Kategori';

        $unit = \App\Models\Category::find($id);

        $this->namaKategori = $unit->nama_kategori;
        $this->deskripsi = $unit->deskripsi;
        $this->icon = $unit->icon;
        $this->id = $unit->id;

        $this->dispatch('show-modal', modalId: 'kategoriModal');
    }

    public function store()
    {
        $this->validate();

        $data = [
            'business_id' => $this->businessId,
            'nama_kategori' => $this->namaKategori,
            'deskripsi' => $this->deskripsi,
            'icon' => $this->icon,
        ];

        if ($this->id) {
            \App\Models\Category::find($this->id)->update($data);
            $message = 'Kategori berhasil diubah';
        } else {
            \App\Models\Category::create($data);
            $message = 'Kategori berhasil ditambahkan';
        }

        $this->dispatch('hide-modal', modalId: 'kategoriModal');
        $this->dispatch('alert', type: 'success', message: $message);
        $this->resetForm();
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        if (\App\Models\Product::where('category_id', $id)->exists()) {
            $this->dispatch('alert', type: 'error', message: 'Kategori tidak dapat dihapus karena ada produk yang terkait');

            return;
        }

        \App\Models\Category::find($id)->delete();
        $this->dispatch('alert', type: 'success', message: 'Kategori berhasil dihapus');
    }

    public function render()
    {
        $this->title = 'Kategori';
        $this->businessId = auth()->user()->business_id;

        $query = \App\Models\Category::where('business_id', $this->businessId);
        $icons = MaterialIcon::getAllIcon();

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('nama_kategori', 'Nama Kategori', true, true),
            TableUtil::setTableHeader('deskripsi', 'Deskripsi', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $categories = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.kategori', [
            'categories' => $categories,
            'icons' => $icons,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
