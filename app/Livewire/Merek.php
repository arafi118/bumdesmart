<?php

namespace App\Livewire;

use App\Traits\WithTable;
use App\Utils\TableUtil;
use Livewire\Attributes\On;
use Livewire\Component;

class Merek extends Component
{
    use WithTable;

    public $title;

    public $titleModal;

    public $id;

    public $businessId;

    public $namaMerek;

    public $deskripsi;

    protected function rules()
    {
        return [
            'namaMerek' => 'required',
            'deskripsi' => 'nullable',
        ];
    }

    public function resetForm()
    {
        $this->reset('namaMerek', 'deskripsi', 'id');
    }

    public function create()
    {
        $this->resetForm();
        $this->titleModal = 'Tambah Merek';

        $this->dispatch('show-modal', modalId: 'merekModal');
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->resetValidation();
        $this->titleModal = 'Ubah Merek';

        $unit = \App\Models\Brand::find($id);

        $this->namaMerek = $unit->nama_brand;
        $this->deskripsi = $unit->deskripsi;
        $this->id = $unit->id;

        $this->dispatch('show-modal', modalId: 'merekModal');
    }

    public function store()
    {
        $this->validate();

        $data = [
            'business_id' => $this->businessId,
            'nama_brand' => $this->namaMerek,
            'deskripsi' => $this->deskripsi,
        ];

        if ($this->id) {
            \App\Models\Brand::find($this->id)->update($data);
            $message = 'Merek berhasil diubah';
        } else {
            \App\Models\Brand::create($data);
            $message = 'Merek berhasil ditambahkan';
        }

        $this->dispatch('hide-modal', modalId: 'merekModal');
        $this->dispatch('alert', type: 'success', message: $message);
        $this->resetForm();
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        \App\Models\Brand::find($id)->delete();
        $this->dispatch('alert', type: 'success', message: 'Merek berhasil dihapus');
    }

    public function render()
    {
        $this->title = 'Merek';
        $this->businessId = auth()->user()->business_id;

        $query = \App\Models\Brand::where('business_id', $this->businessId);

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('nama_brand', 'Nama Merek', true, true),
            TableUtil::setTableHeader('deskripsi', 'Deskripsi', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $brands = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.merek', [
            'brands' => $brands,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
