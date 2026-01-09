<?php

namespace App\Livewire;

use App\Traits\WithTable;
use App\Utils\TableUtil;
use Livewire\Attributes\On;
use Livewire\Component;

class Rak extends Component
{
    use WithTable;

    public $title;

    public $titleModal;

    public $id;

    public $businessId;

    public $kodeRak;

    public $namaRak;

    public $lokasi;

    public $kapasitasMaksimal;

    public $aktif = 1;

    protected function rules()
    {
        return [
            'kodeRak' => 'required',
            'namaRak' => 'required',
            'lokasi' => 'nullable',
            'kapasitasMaksimal' => 'nullable',
            'aktif' => 'nullable',
        ];
    }

    public function resetForm()
    {
        $this->reset('kodeRak', 'namaRak', 'lokasi', 'kapasitasMaksimal', 'aktif', 'id');
    }

    public function create()
    {
        $this->resetForm();
        $this->titleModal = 'Tambah Rak Penyimpanan';

        $this->dispatch('show-modal', modalId: 'rakModal');
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->resetValidation();
        $this->titleModal = 'Ubah Rak Penyimpanan';

        $shelves = \App\Models\Shelves::find($id);

        $this->kodeRak = $shelves->kode_rak;
        $this->namaRak = $shelves->nama_rak;
        $this->lokasi = $shelves->lokasi;
        $this->kapasitas = $shelves->kapasitasMaksimal;
        $this->aktif = $shelves->aktif;
        $this->id = $shelves->id;

        $this->dispatch('show-modal', modalId: 'rakModal');
    }

    public function store()
    {
        $this->validate();

        $data = [
            'business_id' => $this->businessId,
            'kode_rak' => $this->kodeRak,
            'nama_rak' => $this->namaRak,
            'lokasi' => $this->lokasi,
            'kapasitas' => $this->kapasitasMaksimal,
            'aktif' => $this->aktif,
        ];

        if ($this->id) {
            \App\Models\Shelves::find($this->id)->update($data);
            $message = 'Rak berhasil diubah';
        } else {
            \App\Models\Shelves::create($data);
            $message = 'Rak berhasil ditambahkan';
        }

        $this->dispatch('hide-modal', modalId: 'rakModal');
        $this->dispatch('alert', type: 'success', message: $message);
        $this->resetForm();
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        \App\Models\Shelves::find($id)->delete();
        $this->dispatch('alert', type: 'success', message: 'Rak berhasil dihapus');
    }

    public function render()
    {
        $this->title = 'Rak Penyimpanan';
        $this->businessId = auth()->user()->business_id;

        $query = \App\Models\Shelves::where('business_id', $this->businessId);

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('kode_rak', 'Kode Rak', true, true),
            TableUtil::setTableHeader('nama_rak', 'Nama Rak', true, true),
            TableUtil::setTableHeader('lokasi', 'Lokasi', true, true),
            TableUtil::setTableHeader('kapasitas', 'Kapasitas', true, true),
            TableUtil::setTableHeader('aktif', 'Aktif', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $shelves = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.rak', [
            'shelves' => $shelves,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }
}
