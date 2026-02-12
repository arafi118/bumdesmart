<?php

namespace App\Livewire\Keuangan;

use App\Models\Transaction_type;
use App\Models\Account;
use Livewire\Component;

class JurnalUmum extends Component
{
    public $title = 'Jurnal Umum';
    public $business_id;

    public $jenis_transaksi = [];
    public $rekeningList = [];

    public $tanggal_transaksi;
    public $selectedJenis = null;
    public $selectedSumber = null;
    public $selectedTujuan = null;

    public $keterangan;
    public $total = 0;
    public $saldo = 0;

    public $tahun;
    public $bulan;
    public $tanggal;

    public $jurnalUmum = null;

    public function mount()
    {
        $this->business_id = session('business_id');
        $this->jenis_transaksi = Transaction_type::all();
        $this->rekeningList = Account::where('business_id', $this->business_id)->get();
        $this->tanggal_transaksi = date('Y-m-d');
        $this->tahun = date('Y');
        $this->bulan = date('m');
        $this->tanggal = date('d');

        $akun = Account::where('business_id', auth()->user()->business_id)->get();
        $this->jurnalUmum = [
            'akun' => $akun,
            'jenis_transaksi' => $this->jenis_transaksi,
        ];
    }

    public function render()
    {
        return view('livewire.keuangan.jurnal-umum')
            ->layout('layouts.app', ['title' => $this->title]);
    }
}
