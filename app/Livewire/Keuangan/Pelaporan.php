<?php

namespace App\Livewire\Keuangan;

use App\Models\Account;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan')]
class Pelaporan extends Component
{
    // Filter Properties
    public $tahun;

    public $bulan;

    public $periode;

    public $jenis_laporan;

    public $jenis_sub_laporan;

    public $daftarAkun = [];
    public $daftarUser = [];
    public $daftarKategori = [];
    public $daftarPelanggan = [];
    public $daftarSupplier = [];
    public $daftarRak = [];

    public function mount()
    {
        $this->tahun = date('Y');
        $this->bulan = date('m');
        $this->periode = '-';

        $this->jenis_laporan = '';
        $this->jenis_sub_laporan = '';

        $businessId = auth()->user()->business_id;

        $account = Account::where('business_id', $businessId)->get();
        $this->daftarAkun = $account->map(fn ($a) => [
            'id' => $a->id,
            'kode' => $a->kode,
            'nama' => $a->nama,
        ])->toArray();

        $users = \App\Models\User::where('business_id', $businessId)->get();
        $this->daftarUser = $users->map(fn ($u) => [
            'id' => $u->id,
            'nama' => $u->nama_lengkap,
        ])->toArray();

        $this->daftarKategori = \App\Models\Category::where('business_id', $businessId)->get()->map(fn ($c) => [
            'id' => $c->id,
            'nama' => $c->nama_kategori,
        ])->toArray();

        $this->daftarPelanggan = \App\Models\Customer::where('business_id', $businessId)->get()->map(fn ($c) => [
            'id' => $c->id,
            'nama' => $c->nama_pelanggan,
        ])->toArray();

        $this->daftarSupplier = \App\Models\Supplier::where('business_id', $businessId)->get()->map(fn ($s) => [
            'id' => $s->id,
            'nama' => $s->nama_supplier,
        ])->toArray();

        $this->daftarRak = \App\Models\Shelves::where('business_id', $businessId)->get()->map(fn ($r) => [
            'id' => $r->id,
            'nama' => $r->nama_rak,
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.keuangan.pelaporan');
    }
}
